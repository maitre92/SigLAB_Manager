@extends('layouts.admin')

@section('content')
<div class="row g-4">
    <!-- Quick Stats -->
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm stat-card h-100 overflow-hidden" style="background-color: var(--navbar-bg);">
            <div class="card-body p-3 text-white">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="stat-icon-wrapper">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <span class="badge bg-white bg-opacity-25 rounded-pill small" style="font-size: 0.7rem;">Inscrits</span>
                </div>
                <h6 class="text-white text-opacity-75 mb-1 fw-medium small">Total Apprenants</h6>
                <h3 class="fw-bold mb-0 counter-value">{{ $total_apprenants }}</h3>
                <div class="mt-2 progress bg-white bg-opacity-20" style="height: 3px;">
                    <div class="progress-bar bg-white" style="width: 100%"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm stat-card h-100 overflow-hidden" style="background-color: var(--navbar-bg);">
            <div class="card-body p-3 text-white">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="stat-icon-wrapper">
                        <i class="fas fa-laptop-code"></i>
                    </div>
                    <span class="badge bg-white bg-opacity-25 rounded-pill small" style="font-size: 0.7rem;">Actif</span>
                </div>
                <h6 class="text-white text-opacity-75 mb-1 fw-medium small">Sessions Actives</h6>
                <h3 class="fw-bold mb-0 counter-value">{{ $active_formations }}</h3>
                <div class="mt-2 progress bg-white bg-opacity-20" style="height: 3px;">
                    <div class="progress-bar bg-white" style="width: 100%"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm stat-card h-100 overflow-hidden" style="background-color: var(--navbar-bg);">
            <div class="card-body p-3 text-white">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="stat-icon-wrapper">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <span class="badge bg-white bg-opacity-25 rounded-pill small" style="font-size: 0.7rem;">Suivi</span>
                </div>
                <h6 class="text-white text-opacity-75 mb-1 fw-medium small">Évaluations</h6>
                <h3 class="fw-bold mb-0 counter-value">{{ $total_evaluations }}</h3>
                <div class="mt-2 progress bg-white bg-opacity-20" style="height: 3px;">
                    <div class="progress-bar bg-white" style="width: 100%"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm stat-card h-100 overflow-hidden" style="background-color: var(--navbar-bg);">
            <div class="card-body p-3 text-white">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="stat-icon-wrapper">
                        <i class="fas fa-coins"></i>
                    </div>
                    <i class="fas fa-arrow-trend-up text-white-50 small"></i>
                </div>
                <h6 class="text-white text-opacity-75 mb-1 fw-medium small">Recettes Totales</h6>
                <h3 class="fw-bold mb-0 counter-value" style="font-size: 1.3rem;">{{ number_format($total_revenue, 0, ',', ' ') }} <small style="font-size: 0.7rem;">FCFA</small></h3>
                <div class="mt-2 progress bg-white bg-opacity-20" style="height: 3px;">
                    <div class="progress-bar bg-white" style="width: 100%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts & Activities -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-chart-line text-primary me-2"></i> Flux d'inscriptions (6 mois)</h6>
            </div>
            <div class="card-body">
                <canvas id="inscriptionsChart" height="300"></canvas>
            </div>
        </div>

        <div class="card border-0 shadow-sm overflow-hidden">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-0">
                <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-users text-primary me-2"></i> Derniers Apprenants</h6>
                <a href="{{ route('admin.apprenants.index') }}" class="btn btn-sm btn-light border">Voir tout</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0">Apprenant</th>
                            <th class="border-0">Matricule</th>
                            <th class="border-0">Date</th>
                            <th class="border-0 text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recent_apprenants as $app)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-2 me-3 d-flex align-items-center justify-content-center fw-bold" style="width: 40px; height: 40px; font-size: 0.8rem;">
                                            {{ mb_substr($app->prenom ?? '', 0, 1) }}{{ mb_substr($app->nom ?? '', 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark">{{ $app->nom_complet }}</div>
                                            <small class="text-muted">{{ $app->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="badge bg-light text-dark border">{{ $app->matricule }}</span></td>
                                <td>{{ $app->created_at->format('d/m/Y') }}</td>
                                <td class="text-end">
                                    <a href="{{ route('admin.apprenants.show', $app) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">Détails</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center py-4">Aucun apprenant</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Sidebar Info -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4 bg-dark text-white p-2">
            <div class="card-body">
                <h5 class="fw-bold mb-3 text-white">Gestion IT Rapide</h5>
                <p class="small mb-4 text-white text-opacity-75">Suivez vos sessions de formation et générez des attestations professionnelles en quelques clics.</p>
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.formations.create') }}" class="btn btn-primary fw-bold">
                        <i class="fas fa-plus-circle me-1"></i> Créer une session
                    </a>
                    <a href="{{ route('admin.attestations.index') }}" class="btn btn-outline-light border-opacity-25">
                        <i class="fas fa-certificate me-1"></i> Remettre attestation
                    </a>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-calendar-check text-warning me-2"></i> Prochaines Évaluations</h6>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @forelse($upcoming_evaluations as $eval)
                        <div class="list-group-item p-3 border-0 border-bottom border-light">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="fw-bold text-dark small">{{ $eval->titre }}</span>
                                <span class="badge bg-{{ $eval->type == 'examen' ? 'danger' : 'warning' }} bg-opacity-10 text-{{ $eval->type == 'examen' ? 'danger' : 'warning' }} border-0">{{ ucfirst($eval->type) }}</span>
                            </div>
                            <div class="small text-muted mb-2"><i class="fas fa-book-open me-1"></i> {{ $eval->formation->nom }}</div>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="small text-dark fw-medium"><i class="far fa-calendar-alt text-primary me-1"></i> {{ $eval->date_evaluation->format('d M, Y') }}</div>
                                <div class="small text-muted"><i class="far fa-clock me-1"></i> {{ $eval->date_evaluation->format('H:i') }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="p-4 text-center text-muted small">Aucune évaluation prévue</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-trophy text-info me-2"></i> Formations par popularité</h6>
            </div>
            <div class="card-body">
                @foreach($top_formations as $f)
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="small fw-bold text-dark">{{ $f->nom }}</span>
                            <span class="small text-primary fw-bold">{{ $f->apprenants_count }} inscrits</span>
                        </div>
                        <div class="progress" style="height: 8px; border-radius: 4px; background-color: #f0f2f5;">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: {{ ($f->apprenants_count / max(1, $total_apprenants)) * 100 }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<style>
    .stat-card {
        transition: all 0.3s cubic-bezier(.25,.8,.25,1);
        border-radius: 16px;
        position: relative;
        z-index: 1;
        border: none !important;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    }

    .stat-card::after {
        content: "";
        position: absolute;
        top: -15%;
        right: -10%;
        width: 100px;
        height: 100px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        z-index: -1;
    }

    .stat-icon-wrapper {
        width: 40px;
        height: 40px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .counter-value {
        font-size: 1.6rem;
        letter-spacing: -1px;
    }
    
    .card-header {
        border-bottom: 1px solid rgba(0,0,0,0.05) !important;
    }
    
    .table thead th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        color: #6c757d;
        padding: 12px 16px;
    }
</style>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('inscriptionsChart').getContext('2d');
    const chartData = @json($chart_data);
    
    new Chart(ctx, {
        type: 'line',
        data: chartData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                intersect: false,
                mode: 'index',
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1e293b',
                    padding: 12,
                    titleFont: { size: 14 },
                    bodyFont: { size: 13 },
                    cornerRadius: 8,
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1, color: '#94a3b8' },
                    grid: { drawBorder: false, color: '#f1f5f9' }
                },
                x: {
                    ticks: { color: '#94a3b8' },
                    grid: { display: false }
                }
            }
        }
    });
});
</script>
@endsection
