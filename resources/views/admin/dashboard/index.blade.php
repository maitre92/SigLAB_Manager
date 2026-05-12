@extends('layouts.admin')

@section('title', 'Tableau de bord')

@section('content')
<div class="row">
    <!-- Stats Cards -->
    <div class="col-md-3 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-2">Utilisateurs</p>
                        <h3 class="mb-0">-</h3>
                    </div>
                    <div class="text-primary" style="font-size: 2rem;">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-2">Contenu</p>
                        <h3 class="mb-0">-</h3>
                    </div>
                    <div class="text-success" style="font-size: 2rem;">
                        <i class="fas fa-file"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-2">Sessions actives</p>
                        <h3 class="mb-0">-</h3>
                    </div>
                    <div class="text-warning" style="font-size: 2rem;">
                        <i class="fas fa-wifi"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-2">Performance</p>
                        <h3 class="mb-0">100%</h3>
                    </div>
                    <div class="text-info" style="font-size: 2rem;">
                        <i class="fas fa-chart-line"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity & Quick Actions -->
<div class="row mt-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-history"></i> Activité récente</h5>
            </div>
            <div class="card-body">
                <p class="text-muted text-center py-4">Aucune activité pour le moment</p>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-flash"></i> Actions rapides</h5>
            </div>
            <div class="card-body">
                <a href="{{ route('admin.users.create') }}" class="btn btn-sm btn-primary mb-2 w-100">
                    <i class="fas fa-plus"></i> Créer un utilisateur
                </a>
                <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-secondary mb-2 w-100">
                    <i class="fas fa-list"></i> Voir tous les utilisateurs
                </a>
                <a href="{{ route('admin.settings') }}" class="btn btn-sm btn-outline-secondary w-100">
                    <i class="fas fa-cog"></i> Paramètres
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
