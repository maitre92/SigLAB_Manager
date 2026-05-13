@extends('layouts.admin')

@section('title', 'Gestion des Formations')
@php $page_title = 'Gestion des Formations'; @endphp

@section('actions')
    @php 
        $currentUser = Auth::user(); 
    @endphp
    @if(($currentUser && $currentUser->isSuperAdmin()) || ($currentUser && $currentUser->hasPermission('ajouter_formation')))
        <a href="{{ route('admin.formations.create') }}" class="btn text-white shadow-sm" style="background-color: var(--navbar-bg);">
            <i class="fas fa-plus-circle me-1"></i> Ajouter une formation
        </a>
    @endif
@endsection

@section('content')
@php
    $currentUser = Auth::user();
    $isSuperAdmin = $currentUser && $currentUser->isSuperAdmin();
    $canViewDetails = $isSuperAdmin || ($currentUser && $currentUser->hasPermission('voir_details_formation'));
    $canEdit = $isSuperAdmin || ($currentUser && $currentUser->hasPermission('modifier_formation'));
    $canDelete = $isSuperAdmin || ($currentUser && $currentUser->hasPermission('supprimer_formation'));
@endphp

<div class="container-fluid p-0">
    <!-- Filtres -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body py-3">
            <form action="{{ route('admin.formations.index') }}" method="GET" class="row g-2">
                <div class="col-md-4">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Rechercher par nom ou code..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <select name="categorie_id" class="form-select form-select-sm">
                        <option value="">Toutes les catégories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('categorie_id') == $cat->id ? 'selected' : '' }}>{{ $cat->nom }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="statut" class="form-select form-select-sm">
                        <option value="">Tous les statuts</option>
                        <option value="planifiee" {{ request('statut') == 'planifiee' ? 'selected' : '' }}>Planifiée</option>
                        <option value="en_cours" {{ request('statut') == 'en_cours' ? 'selected' : '' }}>En cours</option>
                        <option value="terminee" {{ request('statut') == 'terminee' ? 'selected' : '' }}>Terminée</option>
                        <option value="suspendue" {{ request('statut') == 'suspendue' ? 'selected' : '' }}>Suspendue</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="type" class="form-select form-select-sm">
                        <option value="">Tous les types</option>
                        <option value="Présentiel" {{ request('type') == 'Présentiel' ? 'selected' : '' }}>Présentiel</option>
                        <option value="En ligne" {{ request('type') == 'En ligne' ? 'selected' : '' }}>En ligne</option>
                        <option value="Hybride" {{ request('type') == 'Hybride' ? 'selected' : '' }}>Hybride</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-sm btn-primary w-100">Filtrer</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header text-white" style="background-color: var(--navbar-bg);">
            <h5 class="card-title mb-0"><i class="fas fa-list me-2"></i> Liste des formations</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Formation</th>
                            <th>Code</th>
                            <th>Catégorie</th>
                            <th>Type</th>
                            <th>Coût</th>
                            <th class="text-center">Statut</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($formations as $formation)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-light p-2 me-3 text-primary">
                                        <i class="fas fa-book-open"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $formation->nom }}</div>
                                        <small class="text-muted">{{ $formation->duree_heures }} heures</small>
                                    </div>
                                </div>
                            </td>
                            <td><span class="badge bg-light text-dark border">{{ $formation->code }}</span></td>
                            <td>{{ $formation->categorie->nom ?? 'Non classé' }}</td>
                            <td>
                                @if($formation->type == 'Présentiel')
                                    <span class="text-success small fw-bold"><i class="fas fa-users me-1"></i> Présentiel</span>
                                @elseif($formation->type == 'En ligne')
                                    <span class="text-info small fw-bold"><i class="fas fa-laptop me-1"></i> En ligne</span>
                                @else
                                    <span class="text-warning small fw-bold"><i class="fas fa-exchange-alt me-1"></i> Hybride</span>
                                @endif
                            </td>
                            <td class="fw-bold">{{ number_format($formation->cout, 2, ',', ' ') }} FCFA</td>
                            <td class="text-center">
                                @php
                                    $statusClasses = [
                                        'planifiee' => 'bg-secondary',
                                        'en_cours' => 'bg-primary',
                                        'terminee' => 'bg-success',
                                        'suspendue' => 'bg-danger',
                                    ];
                                    $class = $statusClasses[$formation->statut] ?? 'bg-dark';
                                @endphp
                                <span class="badge {{ $class }}">{{ $formation->statut_label }}</span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group btn-group-sm shadow-sm">
                                    @if($canViewDetails)
                                    <a href="{{ route('admin.formations.show', $formation) }}" class="btn btn-outline-info" title="Voir détails">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @endif
                                    @if($canEdit)
                                    <a href="{{ route('admin.formations.edit', $formation) }}" class="btn btn-outline-primary" title="Modifier la formation">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @endif
                                    <a href="{{ route('admin.apprenants.create', ['formation_id' => $formation->id]) }}" class="btn btn-outline-success" title="Inscrire un apprenant">
                                        <i class="fas fa-user-plus"></i>
                                    </a>
                                    @if($canDelete)
                                    <form action="{{ route('admin.formations.destroy', $formation) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Voulez-vous vraiment supprimer cette formation ?')" title="Supprimer la formation">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fas fa-info-circle fa-2x mb-3 d-block"></i>
                                Aucune formation trouvée.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($formations->hasPages())
        <div class="card-footer bg-white">
            {{ $formations->appends(request()->all())->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
