@extends('layouts.admin')

@section('title', 'Détails de la Formation')
@php $page_title = $formation->code . ': ' . $formation->nom; @endphp

@section('actions')
    @php 
        $currentUser = Auth::user(); 
        $isSuperAdmin = $currentUser && $currentUser->isSuperAdmin();
    @endphp
    <div class="btn-group shadow-sm">
        @if($isSuperAdmin || ($currentUser && $currentUser->hasPermission('modifier_formation')))
        <a href="{{ route('admin.formations.edit', $formation) }}" class="btn text-white" style="background-color: var(--navbar-bg);">
            <i class="fas fa-edit me-1"></i> Modifier la formation
        </a>
        @endif
        @if($isSuperAdmin || ($currentUser && $currentUser->hasPermission('supprimer_formation')))
        <form action="{{ route('admin.formations.destroy', $formation) }}" method="POST" class="d-inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Voulez-vous vraiment supprimer cette formation ?')">
                <i class="fas fa-trash-alt me-1"></i> Supprimer la formation
            </button>
        </form>
        @endif
        <a href="{{ route('admin.formations.index') }}" class="btn btn-light border">
            <i class="fas fa-arrow-left me-1"></i> Retour
        </a>
    </div>
@endsection

@section('content')
<div class="container-fluid p-0">
    <div class="row">
        <div class="col-lg-8">
            <!-- Details Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header text-white d-flex justify-content-between align-items-center" style="background-color: var(--navbar-bg);">
                    <h5 class="card-title mb-0">Informations détaillées</h5>
                    <div class="d-flex align-items-center">
                        @php
                            $statusClasses = [
                                'planifiee' => 'bg-secondary',
                                'en_cours' => 'bg-primary',
                                'terminee' => 'bg-success',
                                'suspendue' => 'bg-danger',
                            ];
                            $class = $statusClasses[$formation->statut] ?? 'bg-dark';
                        @endphp
                        
                        @if($isSuperAdmin || ($currentUser && $currentUser->hasPermission('modifier_formation')))
                        <div class="dropdown me-2">
                            <button class="btn btn-sm btn-light border dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <span class="badge {{ $class }} me-1"> </span> {{ $formation->statut_label }}
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow">
                                <li><h6 class="dropdown-header">Changer le statut</h6></li>
                                <li>
                                    <form action="{{ route('admin.formations.status', $formation) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="statut" value="planifiee">
                                        <button type="submit" class="dropdown-item d-flex justify-content-between align-items-center">Planifiée <span class="badge bg-secondary"> </span></button>
                                    </form>
                                </li>
                                <li>
                                    <form action="{{ route('admin.formations.status', $formation) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="statut" value="en_cours">
                                        <button type="submit" class="dropdown-item d-flex justify-content-between align-items-center">En cours <span class="badge bg-primary"> </span></button>
                                    </form>
                                </li>
                                <li>
                                    <form action="{{ route('admin.formations.status', $formation) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="statut" value="terminee">
                                        <button type="submit" class="dropdown-item d-flex justify-content-between align-items-center">Terminée <span class="badge bg-success"> </span></button>
                                    </form>
                                </li>
                                <li>
                                    <form action="{{ route('admin.formations.status', $formation) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="statut" value="suspendue">
                                        <button type="submit" class="dropdown-item d-flex justify-content-between align-items-center text-danger">Suspendue <span class="badge bg-danger"> </span></button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                        @else
                        <span class="badge {{ $class }} border border-white">{{ $formation->statut_label }}</span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h6 class="text-muted text-uppercase small fw-bold">Description</h6>
                            <p class="mb-0">{{ $formation->description ?: 'Aucune description fournie.' }}</p>
                        </div>
                    </div>
                    <hr>
                    <div class="row g-4">
                        <div class="col-md-4">
                            <h6 class="text-muted text-uppercase small fw-bold">Catégorie</h6>
                            <p class="fw-bold"><i class="fas fa-tag me-2 text-primary"></i> {{ $formation->categorie->nom ?? '---' }}</p>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-muted text-uppercase small fw-bold">Type</h6>
                            <p class="fw-bold"><i class="fas fa-laptop-house me-2 text-primary"></i> {{ $formation->type }}</p>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-muted text-uppercase small fw-bold">Niveau</h6>
                            <p class="fw-bold"><i class="fas fa-layer-group me-2 text-primary"></i> {{ $formation->niveau ?: 'Non spécifié' }}</p>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-muted text-uppercase small fw-bold">Durée</h6>
                            <p class="fw-bold"><i class="fas fa-clock me-2 text-primary"></i> {{ $formation->duree_heures }} heures</p>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-muted text-uppercase small fw-bold">Coût</h6>
                            <p class="fw-bold text-success"><i class="fas fa-money-bill-wave me-2"></i> {{ number_format($formation->cout, 2, ',', ' ') }} FCFA</p>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-muted text-uppercase small fw-bold">Capacité</h6>
                            <p class="fw-bold"><i class="fas fa-users me-2 text-primary"></i> {{ $formation->capacite_max ?: 'Illimitée' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Emploi du temps (Formatted Table) -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom fw-bold d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-calendar-alt me-2 text-primary"></i> Planning & Emploi du temps</span>
                    <span class="badge bg-light text-dark border">
                        {{ $formation->date_debut ? $formation->date_debut->format('d/m/Y') : '?' }} - 
                        {{ $formation->date_fin ? $formation->date_fin->format('d/m/Y') : '?' }}
                    </span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">Jour</th>
                                    <th>Horaire</th>
                                    <th>Activité / Module</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $schedule = json_decode($formation->emploi_du_temps, true) ?: [];
                                @endphp
                                @forelse($schedule as $item)
                                <tr>
                                    <td class="ps-4 fw-bold text-primary">{{ $item['day'] }}</td>
                                    <td>
                                        <span class="badge bg-light text-dark border">
                                            {{ $item['start'] ?? '--:--' }} - {{ $item['end'] ?? '--:--' }}
                                        </span>
                                    </td>
                                    <td>{{ $item['activity'] ?: '---' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted small">
                                        Aucun emploi du temps détaillé n'a été saisi.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Formateurs -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header text-white" style="background-color: var(--navbar-bg);">
                    <h5 class="card-title mb-0"><i class="fas fa-chalkboard-teacher me-2"></i> Formateurs affectés</h5>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($formation->formateurs as $formateur)
                        <li class="list-group-item d-flex align-items-center py-3">
                            <div class="avatar-sm me-3 bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 40px; height: 40px;">
                                {{ strtoupper(substr($formateur->name, 0, 2)) }}
                            </div>
                            <div>
                                <div class="fw-bold">{{ $formateur->name }}</div>
                                <small class="text-muted">{{ $formateur->email }}</small>
                            </div>
                        </li>
                        @empty
                        <li class="list-group-item py-4 text-center text-muted">
                            Aucun formateur affecté.
                        </li>
                        @endforelse
                    </ul>
                </div>
            </div>

            <!-- Metadata -->
            <div class="card border-0 shadow-sm mb-4 bg-light">
                <div class="card-body">
                    <div class="small text-muted mb-2">
                        <i class="fas fa-user-edit me-2 text-primary"></i> Créé par : <strong>{{ $formation->creator->name ?? 'Système' }}</strong>
                    </div>
                    <div class="small text-muted mb-2">
                        <i class="fas fa-calendar-plus me-2 text-primary"></i> Créé le : <strong>{{ $formation->created_at->format('d/m/Y') }}</strong>
                    </div>
                    <div class="small text-muted">
                        <i class="fas fa-history me-2 text-primary"></i> Modifié le : <strong>{{ $formation->updated_at->format('d/m/Y') }}</strong>
                    </div>
                </div>
            </div>

            <!-- Salle -->
            <div class="card border-0 shadow-sm text-center py-4 px-3" style="background: linear-gradient(135deg, var(--navbar-bg) 0%, #1a3a6a 100%);">
                <div class="text-white-50 small mb-2 text-uppercase fw-bold">Lieu / Salle</div>
                <div class="text-white h4 mb-0">
                    <i class="fas fa-map-marker-alt me-2"></i> {{ $formation->salle ?: 'Non défini' }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
