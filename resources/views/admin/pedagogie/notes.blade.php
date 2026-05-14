@extends('layouts.admin')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0 fw-bold">Sélectionnez une évaluation pour saisir les notes</h6>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Date</th>
                    <th>Évaluation / Examen</th>
                    <th>Formation</th>
                    <th>Notes saisies</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($evaluations as $eval)
                    @php
                        $notesCount = $eval->notes->count();
                        $apprenantsCount = $eval->formation->apprenants->count();
                    @endphp
                    <tr>
                        <td>{{ $eval->date_evaluation->format('d/m/Y') }}</td>
                        <td>
                            <div class="fw-bold">{{ $eval->titre }}</div>
                            <span class="badge bg-{{ $eval->type == 'examen' ? 'dark' : 'secondary' }} small">
                                {{ ucfirst($eval->type) }}
                            </span>
                        </td>
                        <td>{{ $eval->formation->nom }}</td>
                        <td>
                            <div class="progress" style="height: 10px; width: 100px;">
                                <div class="progress-bar bg-success" role="progressbar" 
                                     style="width: {{ $apprenantsCount > 0 ? ($notesCount / $apprenantsCount) * 100 : 0 }}%">
                                </div>
                            </div>
                            <small class="text-muted">{{ $notesCount }} / {{ $apprenantsCount }}</small>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.pedagogie.notes.edit', $eval) }}" class="btn btn-sm btn-primary shadow-sm">
                                <i class="fas fa-edit me-1"></i> Saisir les notes
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="fas fa-star mb-3" style="font-size: 48px; opacity: 0.2;"></i>
                            <p class="mb-0">Aucune évaluation disponible.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
