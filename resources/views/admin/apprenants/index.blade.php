@extends('layouts.admin')

@section('title', 'Apprenants')

@section('actions')
    @can('create_learner')
        <a href="{{ route('admin.apprenants.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Ajouter un apprenant
        </a>
    @endcan
@endsection

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <form method="GET" action="{{ route('admin.apprenants.index') }}" class="row g-2 align-items-center flex-grow-1 me-3">
            <div class="col-md-4">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Rechercher par nom, matricule, email..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="statut" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">Tous les statuts</option>
                    @foreach($statuts as $value => $label)
                        <option value="{{ $value }}" {{ request('statut') == $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="niveau_etude" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">Tous les niveaux</option>
                    @foreach($niveaux as $value => $label)
                        <option value="{{ $value }}" {{ request('niveau_etude') == $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                @if(request()->anyFilled(['search', 'statut', 'niveau_etude']))
                    <a href="{{ route('admin.apprenants.index') }}" class="btn btn-sm btn-outline-secondary w-100">
                        <i class="fas fa-times"></i> Réinitialiser
                    </a>
                @else
                    <button type="submit" class="btn btn-sm btn-outline-primary w-100">
                        Filtrer
                    </button>
                @endif
            </div>
        </form>
        @can('create_learner')
            <div>
                <a href="{{ route('admin.apprenants.create') }}" class="btn btn-primary btn-sm text-nowrap">
                    <i class="fas fa-plus"></i> Apprenant
                </a>
            </div>
        @endcan
    </div>
    
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width: 50px;">Photo</th>
                    <th>Matricule</th>
                    <th>Nom Complet</th>
                    <th>Téléphone</th>
                    <th>Formation</th>
                    <th>Statut</th>
                    <th style="width: 120px;" class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($apprenants as $apprenant)
                    <tr>
                        <td>
                            @if($apprenant->photo_url)
                                <img src="{{ $apprenant->photo_url }}" alt="Photo {{ $apprenant->nom }}" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                            @else
                                <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center text-white" style="width: 40px; height: 40px; font-size: 14px; font-weight: bold;">
                                    {{ $apprenant->initiales }}
                                </div>
                            @endif
                        </td>
                        <td><span class="badge bg-light text-dark border">{{ $apprenant->matricule }}</span></td>
                        <td>
                            <div class="fw-bold">{{ $apprenant->nom_complet }}</div>
                            <div class="text-muted small">{{ $apprenant->email ?? 'Sans email' }}</div>
                        </td>
                        <td>{{ $apprenant->telephone ?? '-' }}</td>
                        <td><span class="text-muted">&mdash;</span></td> <!-- Espace réservé pour la future relation formations -->
                        <td>
                            <span class="badge bg-{{ $apprenant->statut->color() ?? 'secondary' }}">
                                {{ $apprenant->statut->label() ?? $apprenant->statut }}
                            </span>
                        </td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm" role="group">
                                @can('view_learner_details')
                                    <a href="{{ route('admin.apprenants.show', $apprenant) }}" class="btn btn-outline-secondary" title="Voir les détails">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                @endcan
                                @can('edit_learner')
                                    <a href="{{ route('admin.apprenants.edit', $apprenant) }}" class="btn btn-outline-primary" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                @endcan
                                @can('delete_learner')
                                    <button type="button" class="btn btn-outline-danger btn-delete-apprenant" 
                                            title="Supprimer"
                                            data-id="{{ $apprenant->id }}"
                                            data-name="{{ $apprenant->nom_complet }}"
                                            data-matricule="{{ $apprenant->matricule }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="fas fa-user-graduate mb-3" style="font-size: 48px; opacity: 0.2;"></i>
                            <p class="mb-0">Aucun apprenant trouvé</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($apprenants->hasPages())
        <div class="card-footer bg-white border-top-0 pt-3">
            {{ $apprenants->links() }}
        </div>
    @endif
</div>

<!-- Modal de confirmation suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-exclamation-triangle me-2"></i> Confirmer la suppression</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer l'apprenant :</p>
                <div class="alert alert-light border">
                    <strong id="deleteApprenantName" class="d-block mb-1"></strong>
                    <small id="deleteApprenantMatricule" class="text-muted"></small>
                </div>
                <p class="text-danger mb-0"><small>Cette action archivera le dossier de l'apprenant.</small></p>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Annuler</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Oui, supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    const deleteBaseUrl = "{{ url('admin/apprenants') }}";
    
    document.querySelectorAll('.btn-delete-apprenant').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const name = this.dataset.name;
            const matricule = this.dataset.matricule;
            
            document.getElementById('deleteApprenantName').textContent = name;
            document.getElementById('deleteApprenantMatricule').textContent = `Matricule: ${matricule}`;
            document.getElementById('deleteForm').action = `${deleteBaseUrl}/${id}`;
            
            deleteModal.show();
        });
    });
});
</script>
@endsection
