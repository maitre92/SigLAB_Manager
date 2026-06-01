@extends('layouts.admin')

@section('title', 'Groupes de formation')

@section('actions')
    <a href="{{ route('admin.groupes-formations.create') }}" class="btn text-white shadow-sm" style="background-color: var(--navbar-bg);">
        <i class="fas fa-plus-circle me-1"></i> Nouveau groupe
    </a>
@endsection

@section('content')
<div class="container-fluid p-0">
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body py-3">
            <form action="{{ route('admin.groupes-formations.index') }}" method="GET" class="row g-2">
                <div class="col-md-4">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="Rechercher groupe, code, formation..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="formation_id" class="form-select form-select-sm">
                        <option value="">Toutes les formations</option>
                        @foreach($formations as $formation)
                            <option value="{{ $formation->id }}" {{ request('formation_id') == $formation->id ? 'selected' : '' }}>
                                {{ $formation->nom }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="statut" class="form-select form-select-sm">
                        <option value="">Tous les statuts</option>
                        <option value="planifiee" {{ request('statut') == 'planifiee' ? 'selected' : '' }}>Planifié</option>
                        <option value="en_cours" {{ request('statut') == 'en_cours' ? 'selected' : '' }}>En cours</option>
                        <option value="terminee" {{ request('statut') == 'terminee' ? 'selected' : '' }}>Terminé</option>
                        <option value="suspendue" {{ request('statut') == 'suspendue' ? 'selected' : '' }}>Suspendu</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-sm btn-primary w-100">
                        <i class="fas fa-filter me-1"></i> Filtrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header text-white" style="background-color: var(--navbar-bg);">
            <h5 class="card-title mb-0"><i class="fas fa-users me-2"></i> Liste des groupes</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Groupe</th>
                            <th>Formation</th>
                            <th>Formateur</th>
                            <th>Apprenants</th>
                            <th>Période</th>
                            <th>Statut</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($groupesFormation as $groupe)
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold text-dark">{{ $groupe->nom }}</div>
                                    <small class="text-muted">{{ $groupe->code }}</small>
                                </td>
                                <td>
                                    <div class="fw-bold">{{ $groupe->formation->nom ?? 'Formation supprimée' }}</div>
                                    <small class="text-muted">{{ $groupe->formation->categorie->nom ?? 'Non classé' }}</small>
                                </td>
                                <td>{{ $groupe->formateurPrincipal->name ?? 'Non défini' }}</td>
                                <td>
                                    <span class="badge bg-light text-dark border">{{ $groupe->apprenants_count }}</span>
                                    @if($groupe->capacite_max)
                                        <small class="text-muted">/ {{ $groupe->capacite_max }}</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="small text-muted">
                                        {{ $groupe->date_debut ? $groupe->date_debut->format('d/m/Y') : '?' }}
                                        -
                                        {{ $groupe->date_fin ? $groupe->date_fin->format('d/m/Y') : '?' }}
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $statusClasses = [
                                            'planifiee' => 'bg-secondary',
                                            'en_cours' => 'bg-primary',
                                            'terminee' => 'bg-success',
                                            'suspendue' => 'bg-danger',
                                        ];
                                        $class = $statusClasses[$groupe->statut] ?? 'bg-dark';
                                    @endphp
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light border dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            <span class="badge {{ $class }} me-1"> </span> {{ $groupe->statut_label }}
                                        </button>
                                        <ul class="dropdown-menu shadow">
                                            @foreach(['planifiee' => ['Planifié', 'bg-secondary'], 'en_cours' => ['En cours', 'bg-primary'], 'terminee' => ['Terminé', 'bg-success'], 'suspendue' => ['Suspendu', 'bg-danger']] as $value => [$label, $badge])
                                                <li>
                                                    <form action="{{ route('admin.groupes-formations.status', $groupe) }}" method="POST">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="statut" value="{{ $value }}">
                                                        <button type="submit" class="dropdown-item d-flex justify-content-between align-items-center {{ $value === 'suspendue' ? 'text-danger' : '' }}">
                                                            {{ $label }} <span class="badge {{ $badge }}"> </span>
                                                        </button>
                                                    </form>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="btn-group btn-group-sm shadow-sm">
                                        <a href="{{ route('admin.apprenants.create', ['groupe_formation_id' => $groupe->id]) }}" class="btn btn-outline-success" title="Inscrire un apprenant">
                                            <i class="fas fa-user-plus"></i>
                                        </a>
                                        <a href="{{ route('admin.groupes-formations.emploi-du-temps.pdf', $groupe) }}" target="_blank" class="btn btn-outline-secondary" title="Imprimer l'emploi du temps">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>
                                        <a href="{{ route('admin.groupes-formations.edit', $groupe) }}" class="btn btn-outline-primary" title="Modifier le groupe">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.groupes-formations.destroy', $groupe) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Archiver ce groupe ?')" title="Archiver">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="fas fa-users fa-2x mb-3 d-block"></i>
                                    Aucun groupe de formation trouvé.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($groupesFormation->hasPages())
            <div class="card-footer bg-white">
                {{ $groupesFormation->appends(request()->all())->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
