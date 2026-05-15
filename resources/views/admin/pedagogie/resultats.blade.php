@extends('layouts.admin')

@section('content')
<div class="container-fluid p-0">
    <!-- Filtre par formation -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px; overflow: hidden;">
        <div class="card-body p-4 bg-white">
            <form action="{{ route('admin.pedagogie.resultats') }}" method="GET" class="row align-items-end g-3">
                <div class="col-md-8">
                    <label class="form-label fw-bold text-muted small text-uppercase mb-2">Sélectionnez une formation pour voir les résultats</label>
                    <select name="formation_id" class="form-select border-0 bg-light py-2 px-3" style="border-radius: 10px;" onchange="this.form.submit()">
                        <option value="">-- Choisir une formation --</option>
                        @foreach($formations as $f)
                            <option value="{{ $f->id }}" {{ $formationId == $f->id ? 'selected' : '' }}>
                                {{ $f->nom }} ({{ $f->code }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary w-100 py-2 shadow-sm" style="border-radius: 10px;">
                        <i class="fas fa-filter me-2"></i> Afficher les résultats
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if($formation)
        <div class="card border-0 shadow-sm" style="border-radius: 15px; overflow: hidden;">
            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-0 fw-bold text-dark">Résultats : {{ $formation->nom }}</h6>
                    <small class="text-muted">{{ $apprenants->count() }} Apprenants | {{ $evaluations->count() }} Évaluations</small>
                </div>
                <button onclick="window.print()" class="btn btn-sm btn-outline-secondary shadow-sm">
                    <i class="fas fa-print me-1"></i> Imprimer
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 py-3 text-muted small text-uppercase" style="min-width: 200px;">Apprenant</th>
                                @foreach($evaluations as $eval)
                                    <th class="text-center py-3 text-muted small text-uppercase" style="min-width: 100px;">
                                        <div class="fw-bold text-dark">{{ $eval->titre }}</div>
                                        <span class="badge bg-{{ $eval->type == 'examen' ? 'dark' : 'secondary' }} small px-1" style="font-size: 9px;">
                                            {{ $eval->type == 'examen' ? 'EX' : 'EV' }} ({{ number_format($eval->coefficient, 1) }})
                                        </span>
                                    </th>
                                @endforeach
                                <th class="pe-4 py-3 text-center text-primary small text-uppercase" style="min-width: 100px; background-color: rgba(13, 110, 253, 0.05);">
                                    Moyenne
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($apprenants as $apprenant)
                                @php
                                    $totalPoints = 0;
                                    $totalCoeff = 0;
                                @endphp
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm me-3 bg-light rounded-circle d-flex align-items-center justify-content-center text-primary fw-bold" style="width: 35px; height: 35px;">
                                                {{ substr($apprenant->prenom, 0, 1) }}{{ substr($apprenant->nom, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark">{{ $apprenant->prenom }} {{ $apprenant->nom }}</div>
                                                <div class="text-muted small">{{ $apprenant->matricule }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    @foreach($evaluations as $eval)
                                        @php
                                            $grade = $notes[$apprenant->id][$eval->id] ?? null;
                                            if ($grade !== null) {
                                                $totalPoints += $grade * $eval->coefficient;
                                                $totalCoeff += $eval->coefficient;
                                            }
                                        @endphp
                                        <td class="text-center">
                                            @if($grade !== null)
                                                <span class="fw-bold {{ $grade < 10 ? 'text-danger' : 'text-success' }}">
                                                    {{ number_format($grade, 2) }}
                                                </span>
                                            @else
                                                <span class="text-muted small italic">N.S.</span>
                                            @endif
                                        </td>
                                    @endforeach
                                    <td class="pe-4 text-center" style="background-color: rgba(13, 110, 253, 0.02);">
                                        @if($totalCoeff > 0)
                                            @php $moyenne = $totalPoints / $totalCoeff; @endphp
                                            <span class="badge {{ $moyenne < 10 ? 'bg-danger' : 'bg-success' }} fs-6 shadow-sm">
                                                {{ number_format($moyenne, 2) }} / 20
                                            </span>
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $evaluations->count() + 2 }}" class="text-center py-5 text-muted">
                                        Aucun apprenant inscrit à cette formation.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white py-3 border-0">
                <div class="row text-center text-muted small">
                    <div class="col-md-4">
                        <i class="fas fa-circle text-success me-1"></i> >= 10 : Admis / Validé
                    </div>
                    <div class="col-md-4">
                        <i class="fas fa-circle text-danger me-1"></i> < 10 : Échec / Rattrapage
                    </div>
                    <div class="col-md-4">
                        <span class="text-muted italic">N.S. : Non Saisi</span>
                    </div>
                </div>
            </div>
        </div>
    @elseif($formationId)
        <div class="text-center py-5">
            <div class="mb-3">
                <i class="fas fa-search fa-3x text-light"></i>
            </div>
            <h5 class="text-muted">Aucune donnée trouvée pour cette formation</h5>
        </div>
    @else
        <div class="text-center py-5 bg-white shadow-sm" style="border-radius: 15px;">
            <div class="mb-3">
                <i class="fas fa-graduation-cap fa-4x" style="color: #e2e8f0;"></i>
            </div>
            <h5 class="text-dark">Sélectionnez une formation</h5>
            <p class="text-muted">Choisissez une formation ci-dessus pour visualiser le récapitulatif des notes.</p>
        </div>
    @endif
</div>

<style>
    .avatar-sm { font-size: 12px; }
    .table thead th { font-weight: 600; }
    .progress { border-radius: 10px; }
    @media print {
        .sidebar, .navbar, .card-header form, .btn-print, .card-footer { display: none !important; }
        .card { border: none !important; shadow: none !important; }
        .container-fluid { padding: 0 !important; }
    }
</style>
@endsection
