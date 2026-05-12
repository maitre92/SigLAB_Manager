<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

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
        return view('admin.dashboard.index', [
            'page_title' => 'Tableau de bord',
            'active_menu' => 'dashboard',
        ]);
    }

    /**
     * Obtenir les statistiques
     */
    public function getStats()
    {
        return $this->success('Statistiques récupérées', [
            'users' => 0,
            'content' => 0,
            'active_sessions' => 0,
            'recent_activity' => [],
        ]);
    }
}
