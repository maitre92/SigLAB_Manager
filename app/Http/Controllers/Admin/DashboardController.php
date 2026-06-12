<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Apprenant;
use App\Models\Depense;
use App\Models\Emargement;
use App\Models\Formation;
use App\Models\GroupeFormation;
use App\Models\Evaluation;
use App\Models\Attestation;
use App\Models\Inscription;
use App\Models\Paiement;
use App\Models\User;
use App\Shared\Enums\UserRole;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Contrôleur pour le tableau de bord administrateur
 */
class DashboardController extends Controller
{
    /**
     * Afficher le tableau de bord
     */
    public function index()
    {
        $user = Auth::user();
        $role = UserRole::tryFrom($user?->role ?? '') ?? UserRole::USER;

        if ($role === UserRole::FORMATEUR) {
            return view('admin.dashboard.index', array_merge($this->trainerDashboard($user), [
                'page_title' => 'Tableau de bord Formateur',
            ]));
        }

        if ($role === UserRole::COMPTABLE) {
            return view('admin.dashboard.index', array_merge($this->accountantDashboard(), [
                'page_title' => 'Tableau de bord Comptable',
            ]));
        }

        if ($role === UserRole::PERSONNEL_ADMINISTRATIF) {
            return view('admin.dashboard.index', array_merge($this->administrativeDashboard(), [
                'page_title' => 'Tableau de bord Administration',
            ]));
        }

        if ($role === UserRole::DIRECTEUR) {
            return view('admin.dashboard.index', array_merge($this->directorDashboard(), [
                'page_title' => 'Tableau de bord Direction',
            ]));
        }

        if ($role === UserRole::MANAGER) {
            return view('admin.dashboard.index', array_merge($this->managerDashboard(), [
                'page_title' => 'Tableau de bord Manager',
            ]));
        }

        return view('admin.dashboard.index', array_merge($this->adminDashboard(), [
            'page_title' => 'Tableau de bord',
        ]));
    }

    private function adminDashboard(): array
    {
        $stats = [
            'total_apprenants' => Apprenant::count(),
            'active_formations' => GroupeFormation::where('statut', 'en_cours')->count(),
            'total_evaluations' => Evaluation::count(),
            'issued_attestations' => Attestation::count(),
            'total_revenue' => (float) DB::table('inscriptions')->sum('montant_paye'),
        ];

        $recent_apprenants = Apprenant::latest()->take(5)->get();
        $upcoming_evaluations = Evaluation::with('formation')
            ->where('date_evaluation', '>=', now())
            ->orderBy('date_evaluation', 'asc')
            ->take(5)
            ->get();
        
        $top_formations = Formation::withCount('apprenants')
            ->orderBy('apprenants_count', 'desc')
            ->take(4)
            ->get();

        // Data for Chart.js (Last 6 months inscriptions)
        $chart_data = $this->getInscriptionsChartData();

        return array_merge($stats, [
            'dashboard_title' => 'Vue globale',
            'dashboard_subtitle' => 'Pilotage général des apprenants, sessions, évaluations et finances.',
            'dashboard_cards' => [
                ['label' => 'Total apprenants', 'value' => $stats['total_apprenants'], 'icon' => 'fas fa-user-graduate', 'badge' => 'Inscrits'],
                ['label' => 'Sessions actives', 'value' => $stats['active_formations'], 'icon' => 'fas fa-laptop-code', 'badge' => 'Actif'],
                ['label' => 'Évaluations', 'value' => $stats['total_evaluations'], 'icon' => 'fas fa-chart-bar', 'badge' => 'Suivi'],
                ['label' => 'Recettes totales', 'value' => number_format($stats['total_revenue'], 0, ',', ' ') . ' FCFA', 'icon' => 'fas fa-coins', 'badge' => 'Finance'],
            ],
            'quick_actions' => [
                ['label' => 'Créer une session', 'route' => 'admin.formations.create', 'icon' => 'fas fa-plus-circle', 'permission' => 'ajouter_formation'],
                ['label' => 'Ajouter apprenant', 'route' => 'admin.apprenants.create', 'icon' => 'fas fa-user-plus', 'permission' => 'create_learner'],
                ['label' => 'Suivi pédagogique', 'route' => 'admin.suivi-pedagogique.index', 'icon' => 'fas fa-user-check', 'permission' => 'view_suivi_pedagogique'],
                ['label' => 'Finances', 'route' => 'admin.finances.index', 'icon' => 'fas fa-money-bill-wave', 'permission' => 'view_finances'],
            ],
            'recent_apprenants' => $recent_apprenants,
            'upcoming_evaluations' => $upcoming_evaluations,
            'top_formations' => $top_formations,
            'chart_data' => $chart_data,
            'dashboard_type' => 'admin',
        ]);
    }

    private function trainerDashboard(User $user): array
    {
        $groupes = GroupeFormation::with(['formation', 'emargements' => fn($query) => $query->where('formateur_id', $user->id)])
            ->whereHas('formateurs', fn($query) => $query->where('users.id', $user->id))
            ->orderBy('date_debut')
            ->get();

        $pending = Emargement::where('formateur_id', $user->id)->where('statut', Emargement::STATUT_EN_ATTENTE)->count();
        $validatedMinutes = Emargement::where('formateur_id', $user->id)->where('statut', Emargement::STATUT_VALIDE)->get()->sum('duree_minutes');

        return [
            'dashboard_title' => 'Espace formateur',
            'dashboard_subtitle' => 'Suivi de vos groupes, emplois du temps, émargements et heures pédagogiques validées.',
            'dashboard_cards' => [
                ['label' => 'Mes groupes', 'value' => $groupes->count(), 'icon' => 'fas fa-users', 'badge' => 'Affectations'],
                ['label' => 'Émargements en attente', 'value' => $pending, 'icon' => 'fas fa-hourglass-half', 'badge' => 'Validation'],
                ['label' => 'Heures validées', 'value' => round($validatedMinutes / 60, 2) . ' h', 'icon' => 'fas fa-clock', 'badge' => 'Réalisé'],
                ['label' => 'Sessions en cours', 'value' => $groupes->where('statut', 'en_cours')->count(), 'icon' => 'fas fa-chalkboard-teacher', 'badge' => 'Actif'],
            ],
            'quick_actions' => [
                ['label' => 'Faire un émargement', 'route' => 'admin.suivi-pedagogique.index', 'icon' => 'fas fa-signature', 'permission' => 'create_emargement'],
            ],
            'trainer_groupes' => $groupes,
            'trainer_schedules' => $groupes->map(function (GroupeFormation $groupe) {
                $schedule = $this->parseSchedule($groupe->emploi_du_temps);

                return [
                    'groupe' => $groupe,
                    'formation' => $groupe->formation,
                    'schedule' => $schedule,
                    'plain_schedule' => $schedule ? null : $groupe->emploi_du_temps,
                ];
            }),
            'recent_emargements' => Emargement::with('groupeFormation.formation')
                ->where('formateur_id', $user->id)
                ->latest()
                ->take(8)
                ->get(),
            'dashboard_type' => 'trainer',
        ];
    }

    private function parseSchedule(?string $emploiDuTemps): array
    {
        if (!$emploiDuTemps) {
            return [];
        }

        $decoded = json_decode($emploiDuTemps, true);

        if (!is_array($decoded)) {
            return [];
        }

        return collect($decoded)
            ->filter(fn($item) => is_array($item))
            ->map(fn($item) => [
                'day' => $item['day'] ?? $item['jour'] ?? '',
                'start' => $item['start'] ?? $item['debut'] ?? '',
                'end' => $item['end'] ?? $item['fin'] ?? '',
                'activity' => $item['activity'] ?? $item['activite'] ?? $item['module'] ?? '',
            ])
            ->values()
            ->all();
    }

    private function accountantDashboard(): array
    {
        $totalRevenue = (float) Paiement::sum('montant');
        $totalExpenses = (float) Depense::sum('montant');

        return [
            'dashboard_title' => 'Espace comptable',
            'dashboard_subtitle' => 'Encaissements, dépenses et rémunérations formateurs à surveiller.',
            'dashboard_cards' => [
                ['label' => 'Recettes', 'value' => number_format($totalRevenue, 0, ',', ' ') . ' FCFA', 'icon' => 'fas fa-arrow-trend-up', 'badge' => 'Entrées'],
                ['label' => 'Dépenses', 'value' => number_format($totalExpenses, 0, ',', ' ') . ' FCFA', 'icon' => 'fas fa-arrow-trend-down', 'badge' => 'Sorties'],
                ['label' => 'Solde', 'value' => number_format($totalRevenue - $totalExpenses, 0, ',', ' ') . ' FCFA', 'icon' => 'fas fa-wallet', 'badge' => 'Caisse'],
                ['label' => 'Paiements du mois', 'value' => Paiement::whereMonth('date_paiement', now()->month)->whereYear('date_paiement', now()->year)->count(), 'icon' => 'fas fa-receipt', 'badge' => 'Mois'],
            ],
            'quick_actions' => [
                ['label' => 'Paiements apprenants', 'route' => 'admin.finances.payments', 'icon' => 'fas fa-credit-card', 'permission' => 'view_payments'],
                ['label' => 'Dépenses', 'route' => 'admin.finances.expenses', 'icon' => 'fas fa-shopping-cart', 'permission' => 'view_expenses'],
                ['label' => 'Rémunérer formateurs', 'route' => 'admin.finances.trainer_payments', 'icon' => 'fas fa-hand-holding-usd', 'permission' => 'view_payments'],
            ],
            'recent_payments' => Paiement::with('inscription.apprenant')->latest()->take(8)->get(),
            'recent_expenses' => Depense::latest()->take(8)->get(),
            'dashboard_type' => 'accountant',
        ];
    }

    private function administrativeDashboard(): array
    {
        return [
            'dashboard_title' => 'Espace administratif',
            'dashboard_subtitle' => 'Gestion opérationnelle des apprenants, inscriptions et attestations.',
            'dashboard_cards' => [
                ['label' => 'Apprenants', 'value' => Apprenant::count(), 'icon' => 'fas fa-user-graduate', 'badge' => 'Dossiers'],
                ['label' => 'Inscriptions actives', 'value' => Inscription::whereNotIn('statut', ['terminee', 'annulee'])->count(), 'icon' => 'fas fa-clipboard-list', 'badge' => 'Actives'],
                ['label' => 'Groupes en cours', 'value' => GroupeFormation::where('statut', 'en_cours')->count(), 'icon' => 'fas fa-users', 'badge' => 'Sessions'],
                ['label' => 'Attestations', 'value' => Attestation::count(), 'icon' => 'fas fa-certificate', 'badge' => 'Docs'],
            ],
            'quick_actions' => [
                ['label' => 'Ajouter apprenant', 'route' => 'admin.apprenants.create', 'icon' => 'fas fa-user-plus', 'permission' => 'create_learner'],
                ['label' => 'Liste apprenants', 'route' => 'admin.apprenants.index', 'icon' => 'fas fa-list', 'permission' => 'view_learners'],
                ['label' => 'Attestations', 'route' => 'admin.attestations.index', 'icon' => 'fas fa-certificate', 'permission' => 'view_certificates'],
            ],
            'recent_apprenants' => Apprenant::latest()->take(8)->get(),
            'dashboard_type' => 'administrative',
        ];
    }

    private function managerDashboard(): array
    {
        $data = $this->administrativeDashboard();
        $data['dashboard_title'] = 'Espace manager';
        $data['dashboard_subtitle'] = 'Coordination des sessions, équipes pédagogiques et validations.';
        $data['dashboard_cards'][] = ['label' => 'Émargements à valider', 'value' => Emargement::where('statut', Emargement::STATUT_EN_ATTENTE)->count(), 'icon' => 'fas fa-user-check', 'badge' => 'À traiter'];
        $data['quick_actions'][] = ['label' => 'Valider émargements', 'route' => 'admin.suivi-pedagogique.index', 'icon' => 'fas fa-check-circle', 'permission' => 'validate_emargement'];
        $data['dashboard_type'] = 'manager';

        return $data;
    }

    private function directorDashboard(): array
    {
        $data = $this->adminDashboard();
        $data['dashboard_title'] = 'Espace direction';
        $data['dashboard_subtitle'] = 'Indicateurs stratégiques et supervision globale du centre.';
        $data['dashboard_type'] = 'director';

        return $data;
    }

    private function getInscriptionsChartData()
    {
        $data = [];
        $labels = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            // Utiliser format standard si translatedFormat pose problème
            $labels[] = $month->format('M'); 
            $data[] = Apprenant::whereMonth('created_at', $month->month)
                ->whereYear('created_at', $month->year)
                ->count();
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Inscriptions',
                    'data' => $data,
                    'backgroundColor' => 'rgba(102, 126, 234, 0.2)',
                    'borderColor' => '#667eea',
                    'borderWidth' => 2,
                    'tension' => 0.4,
                    'fill' => true
                ]
            ]
        ];
    }

    /**
     * Obtenir les statistiques (via AJAX si besoin)
     */
    public function getStats()
    {
        return response()->json([
            'success' => true,
            'data' => [
                'users' => Apprenant::count(),
                'formations' => Formation::count(),
                'active_sessions' => GroupeFormation::where('statut', 'en_cours')->count(),
            ]
        ]);
    }
}
