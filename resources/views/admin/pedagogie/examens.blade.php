@extends('layouts.admin')

@section('actions')
    <button type="button" class="btn text-white shadow-sm" style="background-color: var(--navbar-bg);" data-bs-toggle="modal" data-bs-target="#addExamenModal">
        <i class="fas fa-plus-circle me-1"></i> Programmer un examen
    </button>
@endsection

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0 fw-bold">Calendrier des examens (Session Normale / Rattrapage)</h6>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Date</th>
                    <th>Titre</th>
                    <th>Formation</th>
                    <th>Coeff.</th>
                    <th>Statut</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($examens as $ex)
                    <tr>
                        <td>{{ $ex->date_evaluation->format('d/m/Y') }}</td>
                        <td>
                            <div class="fw-bold">{{ $ex->titre }}</div>
                            <small class="text-muted">{{ $ex->description }}</small>
                        </td>
                        <td>{{ $ex->formation->nom }}</td>
                        <td>{{ number_format($ex->coefficient, 2) }}</td>
                        <td>
                            <span class="badge bg-{{ $ex->statut == 'termine' ? 'success' : ($ex->statut == 'prevu' ? 'primary' : 'danger') }}">
                                {{ ucfirst($ex->statut) }}
                            </span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.pedagogie.resultats', ['formation_id' => $ex->formation_id]) }}" class="btn btn-sm btn-outline-primary" title="Voir les résultats">
                                <i class="fas fa-eye"></i> Résultats
                            </a>
                            <a href="{{ route('admin.pedagogie.notes.edit', $ex) }}" class="btn btn-sm btn-outline-info" title="Saisir les notes">
                                <i class="fas fa-edit"></i> Notes
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="fas fa-pencil-alt mb-3" style="font-size: 48px; opacity: 0.2;"></i>
                            <p class="mb-0">Aucun examen programmé.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Ajout Examen -->
<div class="modal fade" id="addExamenModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Programmer un Examen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.pedagogie.evaluations.store') }}" method="POST">
                @csrf
                <input type="hidden" name="type" value="examen">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Formation</label>
                        <select name="formation_id" class="form-select" required>
                            @foreach($formations as $f)
                                <option value="{{ $f->id }}">{{ $f->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Titre de l'examen</label>
                        <input type="text" name="titre" class="form-control" placeholder="Ex: Examen Final Session 1..." required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date</label>
                            <input type="date" name="date_evaluation" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Coefficient</label>
                            <input type="number" name="coefficient" class="form-control" step="0.5" value="2.0" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description / Consignes</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Programmer</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
