@extends('layouts.admin')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <a href="{{ route('admin.pedagogie.notes') }}" class="btn btn-outline-secondary mb-3">
            <i class="fas fa-arrow-left me-1"></i> Retour à la liste
        </a>
        
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1">{{ $evaluation->titre }}</h4>
                        <p class="text-muted mb-0">
                            <i class="fas fa-book me-1"></i> {{ $evaluation->formation->nom }} | 
                            <i class="fas fa-calendar me-1"></i> {{ $evaluation->date_evaluation->format('d/m/Y') }}
                        </p>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-{{ $evaluation->type == 'examen' ? 'dark' : 'secondary' }} fs-6">
                            {{ ucfirst($evaluation->type) }}
                        </span>
                        <div class="mt-1">Coeff: <strong>{{ number_format($evaluation->coefficient, 2) }}</strong></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <form action="{{ route('admin.pedagogie.notes.store', $evaluation) }}" method="POST">
        @csrf
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 300px;">Apprenant</th>
                        <th style="width: 150px;">Note (/20)</th>
                        <th>Commentaire</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($apprenants as $apprenant)
                        @php
                            $note = $notes[$apprenant->id] ?? null;
                        @endphp
                        <tr>
                            <td>
                                <div class="fw-bold">{{ $apprenant->nom_complet }}</div>
                                <div class="text-muted small">{{ $apprenant->matricule }}</div>
                            </td>
                            <td>
                                <input type="number" name="notes[{{ $apprenant->id }}]" 
                                       class="form-control" step="0.25" min="0" max="20" 
                                       value="{{ $note ? $note->valeur : '' }}" 
                                       placeholder="Note">
                            </td>
                            <td>
                                <input type="text" name="commentaires[{{ $apprenant->id }}]" 
                                       class="form-control" 
                                       value="{{ $note ? $note->commentaire : '' }}" 
                                       placeholder="Observations éventuelles...">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white text-end py-3">
            <button type="submit" class="btn btn-primary px-4 shadow-sm">
                <i class="fas fa-save me-1"></i> Enregistrer les notes
            </button>
        </div>
    </form>
</div>
@endsection
