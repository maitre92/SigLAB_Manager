@extends('layouts.admin')

@section('actions')
    <button type="button" class="btn text-white shadow-sm fw-bold" style="background-color: var(--navbar-bg);" data-bs-toggle="modal" data-bs-target="#addEvaluationModal">
        <i class="fas fa-plus-circle me-1"></i> Nouvelle évaluation
    </button>
@endsection

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0 fw-bold"><i class="fas fa-chart-line text-primary me-2"></i> Liste des évaluations (Contrôles, Quiz, TPs)</h6>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-4">Date</th>
                    <th>Titre / Description</th>
                    <th>Formation & Groupe</th>
                    <th>Coeff.</th>
                    <th>Statut</th>
                    <th class="text-end pe-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($evaluations as $eval)
                    <tr>
                        <td class="ps-4">{{ $eval->date_evaluation->format('d/m/Y') }}</td>
                        <td>
                            <div class="fw-bold text-dark">{{ $eval->titre }}</div>
                            <small class="text-muted">{{ $eval->description ?: 'Aucune description' }}</small>
                        </td>
                        <td>
                            <div class="fw-bold">{{ $eval->formation->nom }}</div>
                            @if($eval->groupe)
                                <span class="badge bg-light text-primary border"><i class="fas fa-layer-group me-1"></i> {{ $eval->groupe->nom }}</span>
                            @else
                                <span class="badge bg-light text-muted border">Tous les groupes</span>
                            @endif
                        </td>
                        <td>{{ number_format($eval->coefficient, 2) }}</td>
                        <td>
                            <span class="badge bg-{{ $eval->statut == 'termine' ? 'success' : ($eval->statut == 'prevu' ? 'primary' : 'danger') }}">
                                {{ ucfirst($eval->statut) }}
                            </span>
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('admin.pedagogie.resultats', $eval->groupe_id ? ['groupe_id' => $eval->groupe_id] : ['formation_id' => $eval->formation_id]) }}" class="btn btn-sm btn-outline-primary" title="Voir les résultats">
                                <i class="fas fa-eye me-1"></i> Résultats
                            </a>
                            <a href="{{ route('admin.pedagogie.notes.edit', $eval) }}" class="btn btn-sm btn-outline-info" title="Saisir les notes">
                                <i class="fas fa-edit me-1"></i> Notes
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="fas fa-chart-line mb-3" style="font-size: 48px; opacity: 0.2;"></i>
                            <p class="mb-0">Aucune évaluation enregistrée.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Ajout Évaluation -->
<div class="modal fade" id="addEvaluationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header text-white" style="background-color: var(--navbar-bg);">
                <h5 class="modal-title"><i class="fas fa-plus me-2"></i> Nouvelle Évaluation</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.pedagogie.evaluations.store') }}" method="POST">
                @csrf
                <input type="hidden" name="type" value="evaluation">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Formation <span class="text-danger">*</span></label>
                        <select name="formation_id" id="formation_select" class="form-select" required>
                            <option value="">Sélectionnez une formation</option>
                            @foreach($formations as $f)
                                <option value="{{ $f->id }}">{{ $f->nom }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3" id="groupe_container" style="display: none;">
                        <label class="form-label">Groupe de formation</label>
                        <select name="groupe_id" id="groupe_select" class="form-select">
                            <option value="">Tous les groupes (Général)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Titre de l'évaluation <span class="text-danger">*</span></label>
                        <input type="text" name="titre" class="form-control" placeholder="Ex: Devoir n°1, Quiz 2..." required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date <span class="text-danger">*</span></label>
                            <input type="date" name="date_evaluation" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Coefficient <span class="text-danger">*</span></label>
                            <input type="number" name="coefficient" class="form-control" step="0.5" value="1.0" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Description ou remarques facultatives..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn text-white" style="background-color: var(--navbar-bg);">Créer</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const formationsData = @json($formations);
        const formationSelect = document.getElementById('formation_select');
        const groupeContainer = document.getElementById('groupe_container');
        const groupeSelect = document.getElementById('groupe_select');

        function updateGroupes() {
            const formationId = formationSelect.value;
            groupeSelect.innerHTML = '<option value="">Tous les groupes (Général)</option>';
            
            if (!formationId) {
                groupeContainer.style.display = 'none';
                return;
            }

            const formation = formationsData.find(f => f.id == formationId);
            if (formation && formation.groupes && formation.groupes.length > 0) {
                formation.groupes.forEach(g => {
                    const option = document.createElement('option');
                    option.value = g.id;
                    option.textContent = g.nom;
                    groupeSelect.appendChild(option);
                });
                groupeContainer.style.display = 'block';
            } else {
                groupeContainer.style.display = 'none';
            }
        }

        formationSelect.addEventListener('change', updateGroupes);
    });
</script>
@endsection

