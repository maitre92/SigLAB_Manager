@extends('layouts.admin')

@section('title', 'Catégories de formation')
@php $page_title = 'Catégories de formation'; @endphp

@section('actions')
    @if(Auth::user()->isSuperAdmin() || Auth::user()->hasAnyPermission(['ajouter_categorie_formation', 'gerer_categories_formations']))
        <button class="btn text-white shadow-sm" style="background-color: var(--navbar-bg);" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
            <i class="fas fa-plus-circle me-1"></i> Nouvelle catégorie
        </button>
    @endif
@endsection

@section('content')
@php
    $currentUser = Auth::user();
    $isSuperAdmin = $currentUser && $currentUser->isSuperAdmin();
    $canCreateCategory = $isSuperAdmin || ($currentUser && $currentUser->hasAnyPermission(['ajouter_categorie_formation', 'gerer_categories_formations']));
    $canEditCategory = $isSuperAdmin || ($currentUser && $currentUser->hasAnyPermission(['modifier_categorie_formation', 'gerer_categories_formations']));
    $canDeleteCategory = $isSuperAdmin || ($currentUser && $currentUser->hasAnyPermission(['supprimer_categorie_formation', 'gerer_categories_formations']));
@endphp
<div class="container-fluid p-0">
    <div class="card border-0 shadow-sm">
        <div class="card-header text-white" style="background-color: var(--navbar-bg);">
            <h5 class="card-title mb-0"><i class="fas fa-tags me-2"></i> Liste des catégories</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Nom</th>
                            <th>Identifiant</th>
                            <th>Description</th>
                            <th class="text-center">Formations</th>
                            <th class="text-center">Statut</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                        <tr>
                            <td class="ps-4 fw-bold">{{ $category->nom }}</td>
                            <td><code class="small">{{ $category->slug }}</code></td>
                            <td>{{ Str::limit($category->description, 50) ?: '---' }}</td>
                            <td class="text-center">
                                <span class="badge rounded-pill bg-info text-dark">
                                    {{ $category->formations_count }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($category->is_active)
                                    <span class="badge bg-success">Actif</span>
                                @else
                                    <span class="badge bg-secondary">Inactif</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                @if($canEditCategory)
                                <button class="btn btn-sm btn-outline-primary me-1" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editCategoryModal{{ $category->id }}"
                                        title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </button>
                                @endif
                                @if($canDeleteCategory)
                                <form action="{{ route('admin.categories-formations.destroy', $category) }}"
                                      method="POST"
                                      class="d-inline"
                                      data-confirm-form
                                      data-confirm-title="{{ $category->formations_count > 0 ? 'Archiver cette catégorie ?' : 'Supprimer définitivement ?' }}"
                                      data-confirm-text="{{ $category->formations_count > 0 ? 'Cette catégorie contient ' . $category->formations_count . ' formation(s). Elle sera archivée et les formations resteront conservées.' : 'Cette catégorie ne contient aucune formation. Elle sera supprimée définitivement.' }}"
                                      data-confirm-icon="{{ $category->formations_count > 0 ? 'warning' : 'error' }}"
                                      data-confirm-button="{{ $category->formations_count > 0 ? 'Oui, archiver' : 'Oui, supprimer' }}">
                                    @csrf
                                    @method('DELETE')
                                    @if($category->formations_count > 0)
                                    <button type="submit" class="btn btn-sm btn-outline-warning"
                                            title="Archiver">
                                        <i class="fas fa-archive"></i>
                                    </button>
                                    @else
                                    <button type="submit" class="btn btn-sm btn-outline-danger"
                                            title="Supprimer définitivement">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    @endif
                                </form>
                                @endif
                            </td>
                        </tr>

                        <!-- Modal Edit -->
                        <div class="modal fade" id="editCategoryModal{{ $category->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header text-white" style="background-color: var(--navbar-bg);">
                                        <h5 class="modal-title">Modifier la catégorie</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form action="{{ route('admin.categories-formations.update', $category) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Nom de la catégorie</label>
                                                <input type="text" name="nom" class="form-control" value="{{ $category->nom }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Description</label>
                                                <textarea name="description" class="form-control" rows="3">{{ $category->description }}</textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                            <button type="submit" class="btn text-white" style="background-color: var(--navbar-bg);">Enregistrer les modifications</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="fas fa-info-circle me-2"></i> Aucune catégorie trouvée.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if($archivedCategories->isNotEmpty())
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-secondary text-white">
            <h5 class="card-title mb-0"><i class="fas fa-archive me-2"></i> Catégories archivées</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Nom</th>
                            <th>Identifiant</th>
                            <th>Description</th>
                            <th class="text-center">Formations</th>
                            <th class="text-center">Statut</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($archivedCategories as $category)
                        <tr>
                            <td class="ps-4 fw-bold">{{ $category->nom }}</td>
                            <td><code class="small">{{ $category->slug }}</code></td>
                            <td>{{ Str::limit($category->description, 50) ?: '---' }}</td>
                            <td class="text-center">
                                <span class="badge rounded-pill bg-info text-dark">
                                    {{ $category->formations_count }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-secondary">Archivée</span>
                            </td>
                            <td class="text-end pe-4">
                                @if($canDeleteCategory)
                                <form action="{{ route('admin.categories-formations.restore', $category->id) }}"
                                      method="POST"
                                      class="d-inline"
                                      data-confirm-form
                                      data-confirm-title="Restaurer cette catégorie ?"
                                      data-confirm-text="Cette catégorie redeviendra disponible pour les formations."
                                      data-confirm-icon="question"
                                      data-confirm-color="#198754"
                                      data-confirm-button="Oui, restaurer">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-outline-success"
                                            title="Restaurer">
                                        <i class="fas fa-undo"></i>
                                    </button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Modal Add -->
@if($canCreateCategory)
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header text-white" style="background-color: var(--navbar-bg);">
                <h5 class="modal-title">Nouvelle catégorie</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.categories-formations.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nom de la catégorie</label>
                        <input type="text" name="nom" class="form-control" placeholder="ex: Programmation, Réseau..." required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Description</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Brève description de la catégorie..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn text-white" style="background-color: var(--navbar-bg);">Créer la catégorie</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
