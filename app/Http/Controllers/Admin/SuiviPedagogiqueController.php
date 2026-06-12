<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Emargement;
use App\Models\GroupeFormation;
use App\Models\SuiviNotification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SuiviPedagogiqueController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $canValidate = $this->canValidate($user);

        $groupesQuery = GroupeFormation::with(['formation', 'formateurs', 'emargements.formateur', 'emargements.validateur'])
            ->whereIn('statut', ['planifiee', 'en_cours'])
            ->whereHas('formateurs');

        if (!$canValidate) {
            $groupesQuery->whereHas('formateurs', function ($query) use ($user) {
                $query->where('users.id', $user->id);
            });
        }

        $groupes = $groupesQuery->orderBy('date_debut')->orderBy('nom')->get();
        $suggestedTimes = $groupes->mapWithKeys(fn(GroupeFormation $groupe) => [
            $groupe->id => $this->suggestTimesForGroup($groupe),
        ]);

        $emargementsQuery = Emargement::with(['groupeFormation.formation', 'formateur', 'validateur'])
            ->latest();

        if (!$canValidate) {
            $emargementsQuery->where('formateur_id', $user->id);
        }

        $emargements = $emargementsQuery->paginate(20);

        return view('admin.suivi-pedagogique.index', compact('groupes', 'emargements', 'canValidate', 'suggestedTimes'));
    }

    public function storeEmargement(Request $request)
    {
        $request->validate([
            'groupe_formation_id' => 'required|exists:groupes_formation,id',
            'date_seance' => 'required|date',
            'heure_debut' => 'required|date_format:H:i',
            'heure_fin' => 'required|date_format:H:i|after:heure_debut',
            'titre_realise' => 'required|string|max:255',
        ]);

        $user = Auth::user();
        $groupe = GroupeFormation::with('formation')
            ->whereIn('statut', ['planifiee', 'en_cours'])
            ->findOrFail($request->groupe_formation_id);

        $isAssigned = $groupe->formateurs()
            ->where('users.id', $user->id)
            ->exists();

        if (!$isAssigned) {
            return back()
                ->withInput()
                ->withErrors(['groupe_formation_id' => "Vous ne pouvez émarger que sur un groupe qui vous est affecté."]);
        }

        $emargement = DB::transaction(function () use ($request, $user) {
            $emargement = Emargement::create([
                'groupe_formation_id' => $request->groupe_formation_id,
                'formateur_id' => $user->id,
                'date_seance' => $request->date_seance,
                'heure_debut' => $request->heure_debut,
                'heure_fin' => $request->heure_fin,
                'titre_realise' => $request->titre_realise,
                'statut' => Emargement::STATUT_EN_ATTENTE,
            ]);

            $this->notifyValidators($emargement);

            return $emargement;
        });

        return redirect()
            ->route('admin.suivi-pedagogique.index')
            ->with('success', "Émargement soumis avec succès. Il est maintenant en attente de validation.");
    }

    public function validateEmargement(Emargement $emargement)
    {
        abort_unless($this->canValidate(Auth::user()), 403);

        $emargement->update([
            'statut' => Emargement::STATUT_VALIDE,
            'validated_by' => Auth::id(),
            'validated_at' => now(),
            'motif_rejet' => null,
        ]);

        $this->markEmargementNotificationsAsRead($emargement);

        return back()->with('success', "Émargement validé avec succès.");
    }

    public function rejectEmargement(Request $request, Emargement $emargement)
    {
        abort_unless($this->canValidate(Auth::user()), 403);

        $request->validate([
            'motif_rejet' => 'required|string|max:1000',
        ]);

        $emargement->update([
            'statut' => Emargement::STATUT_REJETE,
            'validated_by' => Auth::id(),
            'validated_at' => now(),
            'motif_rejet' => $request->motif_rejet,
        ]);

        $this->markEmargementNotificationsAsRead($emargement);

        return back()->with('success', "Émargement rejeté.");
    }

    public function readNotifications()
    {
        SuiviNotification::where('user_id', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return redirect()->route('admin.suivi-pedagogique.index');
    }

    private function canValidate(User $user): bool
    {
        return $user->isSuperAdmin() || $user->hasPermission('validate_emargement');
    }

    private function notifyValidators(Emargement $emargement): void
    {
        $emargement->loadMissing(['formateur', 'groupeFormation.formation']);

        $validators = User::query()
            ->where(function ($query) {
                $query->where('role', 'superadmin')
                    ->orWhere('role', 'admin')
                    ->orWhereHas('permissions', function ($permissionQuery) {
                        $permissionQuery->where('slug', 'validate_emargement')
                            ->where('is_active', true);
                    });
            })
            ->where('id', '!=', $emargement->formateur_id)
            ->get();

        foreach ($validators as $validator) {
            SuiviNotification::create([
                'user_id' => $validator->id,
                'emargement_id' => $emargement->id,
                'titre' => 'Nouvel émargement à valider',
                'message' => $emargement->formateur->name . ' a soumis une séance pour '
                    . ($emargement->groupeFormation->nom ?? 'un groupe') . '.',
            ]);
        }
    }

    private function markEmargementNotificationsAsRead(Emargement $emargement): void
    {
        SuiviNotification::where('emargement_id', $emargement->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    private function suggestTimesForGroup(GroupeFormation $groupe): array
    {
        $now = now();
        $fallbackStart = $now->copy()->format('H:i');
        $fallbackEnd = $now->copy()->addHour()->format('H:i');
        $dayName = $this->frenchDayName($now);
        $schedule = json_decode($groupe->emploi_du_temps ?? '', true);

        if (is_array($schedule)) {
            $slot = collect($schedule)
                ->filter(fn($item) => is_array($item))
                ->first(function ($item) use ($dayName) {
                    $itemDay = $item['day'] ?? $item['jour'] ?? null;

                    return $itemDay && mb_strtolower($itemDay) === mb_strtolower($dayName);
                });

            $start = $slot['start'] ?? $slot['debut'] ?? null;
            $end = $slot['end'] ?? $slot['fin'] ?? null;

            if ($start && $end) {
                return [
                    'date' => $now->format('Y-m-d'),
                    'start' => substr($start, 0, 5),
                    'end' => substr($end, 0, 5),
                ];
            }
        }

        return [
            'date' => $now->format('Y-m-d'),
            'start' => $fallbackStart,
            'end' => $fallbackEnd,
        ];
    }

    private function frenchDayName(Carbon $date): string
    {
        return [
            1 => 'Lundi',
            2 => 'Mardi',
            3 => 'Mercredi',
            4 => 'Jeudi',
            5 => 'Vendredi',
            6 => 'Samedi',
            0 => 'Dimanche',
        ][(int) $date->format('w')];
    }
}
