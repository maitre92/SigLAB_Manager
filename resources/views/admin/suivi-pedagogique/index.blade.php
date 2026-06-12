@extends('layouts.admin')

@section('title', 'Suivi Pédagogique')

@section('content')
@php
    $user = Auth::user();
    $isFormateur = $user && (string) $user->role === \App\Shared\Enums\UserRole::FORMATEUR->value;
    $canCreateEmargement = $user && ($user->isSuperAdmin() || $isFormateur || $user->hasPermission('create_emargement'));
    $statusClasses = [
        'en_attente' => 'bg-warning text-dark',
        'valide' => 'bg-success',
        'rejete' => 'bg-danger',
    ];
    $statusLabels = [
        'en_attente' => 'En attente',
        'valide' => 'Validé',
        'rejete' => 'Rejeté',
    ];
    $emargementGroups = $canCreateEmargement
        ? $groupes->filter(fn($groupe) => $groupe->formateurs->contains('id', $user->id))->values()
        : collect();
@endphp

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header border-0 py-3" style="background-color: var(--navbar-bg);">
                <h6 class="mb-0 fw-bold text-white">
                    <i class="fas fa-user-check text-warning me-2"></i> Groupes affectés
                </h6>
            </div>
            <div class="card-body">
                @forelse($groupes as $groupe)
                    @php
                        $validatedMinutes = $groupe->emargements
                            ->where('statut', 'valide')
                            ->when(!$canValidate, fn($items) => $items->where('formateur_id', $user->id))
                            ->sum('duree_minutes');
                        $validatedHours = round($validatedMinutes / 60, 2);
                        $plannedHours = $groupe->formation->duree_heures ?? 0;
                        $percent = $plannedHours > 0 ? min(100, round(($validatedHours / $plannedHours) * 100)) : 0;
                        $times = $suggestedTimes[$groupe->id] ?? ['date' => date('Y-m-d'), 'start' => now()->format('H:i'), 'end' => now()->addHour()->format('H:i')];
                    @endphp
                    <div class="border rounded p-3 mb-3">
                        <div class="d-flex justify-content-between align-items-start gap-2">
                            <div>
                                <div class="fw-bold">{{ $groupe->nom }}</div>
                                <div class="small text-muted">{{ $groupe->formation->nom ?? 'Formation non définie' }}</div>
                            </div>
                            <span class="badge bg-light text-dark border">{{ $groupe->statut_label }}</span>
                        </div>

                        <div class="mt-3">
                            <div class="d-flex justify-content-between small mb-1">
                                <span>Heures validées</span>
                                <strong>{{ $validatedHours }} / {{ $plannedHours }} h</strong>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $percent }}%;"></div>
                            </div>
                        </div>

                    </div>

                    <div class="modal fade" id="emargementModal{{ $groupe->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header bg-dark text-white">
                                    <h5 class="modal-title">
                                        <i class="fas fa-signature text-warning me-2"></i> Émargement
                                    </h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
                                </div>
                                <form action="{{ route('admin.suivi-pedagogique.emargements.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="groupe_formation_id" value="{{ $groupe->id }}">
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label small fw-bold">Groupe</label>
                                            <input type="text" class="form-control bg-light" value="{{ $groupe->nom }} - {{ $groupe->formation->nom ?? 'Formation' }}" readonly>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label small fw-bold">Formateur</label>
                                            <input type="text" class="form-control bg-light" value="{{ $user->name }}" readonly>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-4">
                                                <label class="form-label small fw-bold">Date</label>
                                                <input type="date" name="date_seance" class="form-control" value="{{ $times['date'] }}" required>
                                            </div>
                                            <div class="col-4">
                                                <label class="form-label small fw-bold">Début</label>
                                                <input type="time" name="heure_debut" class="form-control js-start-time" value="{{ $times['start'] }}" required>
                                            </div>
                                            <div class="col-4">
                                                <label class="form-label small fw-bold">Fin</label>
                                                <input type="time" name="heure_fin" class="form-control js-end-time" value="{{ $times['end'] }}" required>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label small fw-bold">Titre de ce qui a été fait</label>
                                            <input type="text" name="titre_realise" class="form-control" maxlength="255" placeholder="Ex: Introduction à Laravel" required>
                                        </div>
                                        <div class="alert alert-info small mb-0">
                                            <i class="fas fa-lock me-1"></i> Le groupe est fixé automatiquement par votre affectation.
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                        <button type="submit" class="btn btn-primary">Soumettre</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-folder-open fa-2x mb-2"></i>
                        <div>Aucun groupe disponible.</div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold">
                    <i class="fas fa-clipboard-list text-primary me-2"></i> Émargements
                </h6>
                @if($emargementGroups->count() === 1)
                    <button type="button"
                            class="btn btn-sm text-white shadow-sm"
                            style="background-color: var(--navbar-bg);"
                            data-bs-toggle="modal"
                            data-bs-target="#emargementModal{{ $emargementGroups->first()->id }}">
                        <i class="fas fa-plus-circle me-1"></i> Émarger
                    </button>
                @elseif($emargementGroups->count() > 1)
                    <div class="dropdown">
                        <button type="button"
                                class="btn btn-sm text-white shadow-sm dropdown-toggle"
                                style="background-color: var(--navbar-bg);"
                                data-bs-toggle="dropdown"
                                aria-expanded="false">
                            <i class="fas fa-plus-circle me-1"></i> Émarger
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            @foreach($emargementGroups as $emargementGroup)
                                <li>
                                    <button type="button"
                                            class="dropdown-item"
                                            data-bs-toggle="modal"
                                            data-bs-target="#emargementModal{{ $emargementGroup->id }}">
                                        <span class="fw-bold">{{ $emargementGroup->nom }}</span>
                                        <span class="d-block small text-muted">{{ $emargementGroup->formation->nom ?? 'Formation' }}</span>
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-uppercase" style="font-size: 0.7rem;">
                        <tr>
                            <th class="px-4">Séance</th>
                            <th>Formateur / Groupe</th>
                            <th>Réalisation</th>
                            <th class="text-center">Durée</th>
                            <th class="text-center">Statut</th>
                            @if($canValidate)
                                <th class="text-center px-4">Action</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($emargements as $emargement)
                            <tr>
                                <td class="px-4">
                                    <div class="fw-bold">{{ $emargement->date_seance->format('d/m/Y') }}</div>
                                    <div class="small text-muted">{{ substr($emargement->heure_debut, 0, 5) }} - {{ substr($emargement->heure_fin, 0, 5) }}</div>
                                </td>
                                <td>
                                    <div class="fw-bold">{{ $emargement->formateur->name ?? 'N/A' }}</div>
                                    <div class="small text-muted">{{ $emargement->groupeFormation->nom ?? 'Groupe supprimé' }}</div>
                                    <div class="small text-muted">{{ $emargement->groupeFormation->formation->nom ?? 'Formation non définie' }}</div>
                                </td>
                                <td>
                                    <div>{{ $emargement->titre_realise }}</div>
                                    @if($emargement->motif_rejet)
                                        <div class="small text-danger">Motif : {{ $emargement->motif_rejet }}</div>
                                    @endif
                                </td>
                                <td class="text-center fw-bold">{{ $emargement->duree_heures }} h</td>
                                <td class="text-center">
                                    <span class="badge {{ $statusClasses[$emargement->statut] ?? 'bg-secondary' }}">
                                        {{ $statusLabels[$emargement->statut] ?? $emargement->statut }}
                                    </span>
                                </td>
                                @if($canValidate)
                                    <td class="text-center px-4">
                                        @if($emargement->statut === 'en_attente')
                                            <div class="d-flex justify-content-center gap-2">
                                                <form action="{{ route('admin.suivi-pedagogique.emargements.validate', $emargement) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-sm btn-outline-success" title="Valider">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                                <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $emargement->id }}" title="Rejeter">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        @else
                                            <span class="small text-muted">{{ $emargement->validateur->name ?? '-' }}</span>
                                        @endif
                                    </td>
                                @endif
                            </tr>

                            <div class="modal fade" id="rejectModal{{ $emargement->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header bg-danger text-white">
                                            <h5 class="modal-title">Rejeter l'émargement</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
                                        </div>
                                        <form action="{{ route('admin.suivi-pedagogique.emargements.reject', $emargement) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <div class="modal-body">
                                                <label class="form-label small fw-bold">Motif du rejet</label>
                                                <textarea name="motif_rejet" class="form-control" rows="4" required></textarea>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                <button type="submit" class="btn btn-danger">Rejeter</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="{{ $canValidate ? 6 : 5 }}" class="text-center py-5 text-muted">
                                    Aucun émargement enregistré.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($emargements->hasPages())
                <div class="card-footer bg-white">
                    {{ $emargements->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
