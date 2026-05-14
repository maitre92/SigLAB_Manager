@extends('layouts.admin')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold">Attestations Délivrées</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Référence</th>
                            <th>Apprenant</th>
                            <th>Formation</th>
                            <th>Date</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attestations as $att)
                            <tr>
                                <td><span class="badge bg-light text-dark border">{{ $att->reference }}</span></td>
                                <td>
                                    <div class="fw-bold">{{ $att->apprenant->nom_complet }}</div>
                                    <small class="text-muted">{{ $att->apprenant->matricule }}</small>
                                </td>
                                <td>{{ $att->formation->nom }}</td>
                                <td>{{ $att->date_emission->format('d/m/Y') }}</td>
                                <td class="text-end">
                                    <a href="{{ route('admin.attestations.show', $att) }}" class="btn btn-sm btn-outline-primary" title="Voir / Imprimer">
                                        <i class="fas fa-print"></i>
                                    </a>
                                    <form action="{{ route('admin.attestations.destroy', $att) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Supprimer cette attestation ?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="fas fa-certificate mb-3" style="font-size: 48px; opacity: 0.2;"></i>
                                    <p class="mb-0">Aucune attestation générée pour le moment.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold">Générer pour une formation terminée</h6>
            </div>
            <div class="card-body">
                <p class="small text-muted">Sélectionnez une formation pour voir la liste des apprenants éligibles.</p>
                <div class="list-group list-group-flush">
                    @forelse($formations as $f)
                        <div class="list-group-item px-0">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-bold">{{ $f->nom }}</span>
                                <span class="badge bg-success">Terminée</span>
                            </div>
                            <button class="btn btn-sm btn-primary w-100" type="button" data-bs-toggle="collapse" data-bs-target="#f-{{ $f->id }}">
                                <i class="fas fa-users me-1"></i> Voir les apprenants
                            </button>
                            <div class="collapse mt-2" id="f-{{ $f->id }}">
                                <ul class="list-unstyled small mb-0">
                                    @foreach($f->apprenants as $app)
                                        <li class="d-flex justify-content-between align-items-center py-1 border-bottom border-light">
                                            <span>{{ $app->nom_complet }}</span>
                                            <a href="{{ route('admin.attestations.store') }}?apprenant_id={{ $app->id }}&formation_id={{ $f->id }}&date_emission={{ date('Y-m-d') }}" 
                                               onclick="event.preventDefault(); document.getElementById('gen-form-{{ $app->id }}-{{ $f->id }}').submit();"
                                               class="text-primary" title="Générer maintenant">
                                                <i class="fas fa-plus-circle"></i>
                                            </a>
                                            <form id="gen-form-{{ $app->id }}-{{ $f->id }}" action="{{ route('admin.attestations.store') }}" method="POST" style="display: none;">
                                                @csrf
                                                <input type="hidden" name="apprenant_id" value="{{ $app->id }}">
                                                <input type="hidden" name="formation_id" value="{{ $f->id }}">
                                                <input type="hidden" name="date_emission" value="{{ date('Y-m-d') }}">
                                            </form>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-muted my-3">Aucune formation terminée.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
