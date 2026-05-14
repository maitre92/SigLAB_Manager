<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Apprenant;
use App\Models\Formation;
use App\Models\Evaluation;
use App\Models\Attestation;
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
        $stats = [
            'total_apprenants' => Apprenant::count(),
            'active_formations' => Formation::where('statut', 'en_cours')->count(),
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

        return view('admin.dashboard.index', array_merge($stats, [
            'page_title' => 'Tableau de bord',
            'recent_apprenants' => $recent_apprenants,
            'upcoming_evaluations' => $upcoming_evaluations,
            'top_formations' => $top_formations,
            'chart_data' => $chart_data,
        ]));
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
                'active_sessions' => Formation::where('statut', 'en_cours')->count(),
            ]
        ]);
    }
}
