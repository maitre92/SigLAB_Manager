<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Formation;
use App\Models\CategorieFormation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FormationController extends Controller
{
    public function index(Request $request)
    {
        $query = Formation::with(['categorie', 'formateurs']);

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($request->has('categorie_id') && $request->categorie_id != '') {
            $query->where('categorie_formation_id', $request->categorie_id);
        }

        if ($request->has('statut') && $request->statut != '') {
            $query->where('statut', $request->statut);
        }

        if ($request->has('type') && $request->type != '') {
            $query->where('type', $request->type);
        }

        $formations = $query->latest()->paginate(10);
        $categories = CategorieFormation::where('is_active', true)->orderBy('nom')->get();

        return view('admin.formations.index', compact('formations', 'categories'));
    }

    public function create()
    {
        $categories = CategorieFormation::where('is_active', true)->orderBy('nom')->get();
        $formateurs = User::where('role', '!=', \App\Shared\Enums\UserRole::SUPERADMIN->value)
            ->orderBy('name')
            ->get();
        return view('admin.formations.create', compact('categories', 'formateurs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:formations,code',
            'categorie_formation_id' => 'required|exists:categorie_formations,id',
            'type' => 'required|in:Présentiel,En ligne,Hybride',
            'duree_heures' => 'nullable|integer|min:0',
            'cout' => 'nullable|numeric|min:0',
            'capacite_max' => 'nullable|integer|min:0',
            'niveau' => 'nullable|string|max:100',
            'statut' => 'required|in:planifiee,en_cours,terminee,suspendue',
            'salle' => 'nullable|string|max:255',
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date|after_or_equal:date_debut',
            'emploi_du_temps' => 'nullable|string',
            'formateurs' => 'nullable|array',
            'formateurs.*' => 'exists:users,id',
        ]);

        $validated['created_by'] = Auth::id();

        $formation = Formation::create($validated);

        if ($request->has('formateurs')) {
            $formation->formateurs()->sync($request->formateurs);
        }

        return redirect()->route('admin.formations.index')->with('success', 'Formation créée avec succès.');
    }

    public function show(Formation $formation)
    {
        $formation->load(['categorie', 'formateurs', 'creator']);
        return view('admin.formations.show', compact('formation'));
    }

    public function edit(Formation $formation)
    {
        $categories = CategorieFormation::where('is_active', true)->orderBy('nom')->get();
        $formateurs = User::where('role', '!=', \App\Shared\Enums\UserRole::SUPERADMIN->value)
            ->orderBy('name')
            ->get();
        $selectedFormateurs = $formation->formateurs->pluck('id')->toArray();
        return view('admin.formations.edit', compact('formation', 'categories', 'formateurs', 'selectedFormateurs'));
    }

    public function update(Request $request, Formation $formation)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:formations,code,' . $formation->id,
            'categorie_formation_id' => 'required|exists:categorie_formations,id',
            'type' => 'required|in:Présentiel,En ligne,Hybride',
            'duree_heures' => 'nullable|integer|min:0',
            'cout' => 'nullable|numeric|min:0',
            'capacite_max' => 'nullable|integer|min:0',
            'niveau' => 'nullable|string|max:100',
            'statut' => 'required|in:planifiee,en_cours,terminee,suspendue',
            'salle' => 'nullable|string|max:255',
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date|after_or_equal:date_debut',
            'emploi_du_temps' => 'nullable|string',
            'formateurs' => 'nullable|array',
            'formateurs.*' => 'exists:users,id',
        ]);

        $formation->update($validated);

        if ($request->has('formateurs')) {
            $formation->formateurs()->sync($request->formateurs);
        } else {
            $formation->formateurs()->detach();
        }

        return redirect()->route('admin.formations.index')->with('success', 'Formation mise à jour avec succès.');
    }

    public function destroy(Formation $formation)
    {
        $formation->delete();
        return redirect()->route('admin.formations.index')->with('success', 'Formation supprimée (archivée) avec succès.');
    }

    public function byFormateur(User $formateur)
    {
        $formations = $formateur->formations()->with('categorie')->latest()->paginate(10);
        return view('admin.formations.index', compact('formations', 'formateur'));
    }

    public function updateStatus(Request $request, Formation $formation)
    {
        $validated = $request->validate([
            'statut' => 'required|in:planifiee,en_cours,terminee,suspendue',
        ]);

        $formation->update(['statut' => $validated['statut']]);

        return back()->with('success', 'Le statut de la formation a été mis à jour.');
    }
}
