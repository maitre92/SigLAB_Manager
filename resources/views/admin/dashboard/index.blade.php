@extends('layouts.admin')

@section('content')
<div class="row g-4">
    <!-- Quick Stats -->
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm overflow-hidden h-100" style="background: linear-gradient(135deg, #4e54c8 0%, #8f94fb 100%);">
            <div class="card-body p-4 position-relative">
                <div class="position-absolute top-0 end-0 p-3 opacity-25">
                    <i class="fas fa-user-graduate fa-4x text-white"></i>
                </div>
                <p class="text-white text-opacity-75 mb-1 fw-bold text-uppercase small">Total Apprenants</p>
                <h2 class="text-white mb-2 fw-bold">{{ $total_apprenants }}</h2>
                <div class="text-white text-opacity-75 small">
                    <i class="fas fa-arrow-up me-1"></i> Inscriptions globales
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm overflow-hidden h-100" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
            <div class="card-body p-4 position-relative">
                <div class="position-absolute top-0 end-0 p-3 opacity-25">
                    <i class="fas fa-laptop-code fa-4x text-white"></i>
                </div>
                <p class="text-white text-opacity-75 mb-1 fw-bold text-uppercase small">Sessions Actives</p>
                <h2 class="text-white mb-2 fw-bold">{{ $active_formations }}</h2>
                <div class="text-white text-opacity-75 small">
                    <i class="fas fa-clock me-1"></i> Formations en cours
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm overflow-hidden h-100" style="background: linear-gradient(135deg, #f2994a 0%, #f2c94c 100%);">
            <div class="card-body p-4 position-relative">
                <div class="position-absolute top-0 end-0 p-3 opacity-25">
                    <i class="fas fa-chart-bar fa-4x text-white"></i>
                </div>
                <p class="text-white text-opacity-75 mb-1 fw-bold text-uppercase small">Évaluations</p>
                <h2 class="text-white mb-2 fw-bold">{{ $total_evaluations }}</h2>
                <div class="text-white text-opacity-75 small">
                    <i class="fas fa-pencil-alt me-1"></i> Suivi pédagogique
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm overflow-hidden h-100" style="background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);">
            <div class="card-body p-4 position-relative">
                <div class="position-absolute top-0 end-0 p-3 opacity-25">
                    <i class="fas fa-coins fa-4x text-white"></i>
                </div>
                <p class="text-white text-opacity-75 mb-1 fw-bold text-uppercase small">Recettes Totales</p>
                <h2 class="text-white mb-2 fw-bold">{{ number_format($total_revenue, 0, ',', ' ') }} FCFA</h2>
                <div class="text-white text-opacity-75 small">
                    <i class="fas fa-money-bill-wave me-1"></i> Paiements encaissés
                </div>
            </div>
        </div>
    </div>

    <!-- Charts & Activities -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold"><i class="fas fa-chart-line text-primary me-2"></i> Flux d'inscriptions</h6>
            </div>
            <div class="card-body">
                <canvas id="inscriptionsChart" height="280"></canvas>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold"><i class="fas fa-users text-primary me-2"></i> Derniers Inscrits</h6>
                <a href="{{ route('admin.apprenants.index') }}" class="btn btn-sm btn-light">Voir tout</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Apprenant</th>
                            <th>Matricule</th>
                            <th>Date</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recent_apprenants as $app)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-2 me-3" style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; font-weight: bold;">
                                            {{ mb_substr($app->prenom ?? '', 0, 1) }}{{ mb_substr($app->nom ?? '', 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold">{{ $app->nom_complet }}</div>
                                            <small class="text-muted">{{ $app->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="badge bg-light text-dark border">{{ $app->matricule }}</span></td>
                                <td>{{ $app->created_at->format('d/m/Y') }}</td>
                                <td class="text-end">
                                    <a href="{{ route('admin.apprenants.show', $app) }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-eye"></i></a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Sidebar Info -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4 bg-primary text-white p-2">
            <div class="card-body">
                <h5 class="fw-bold mb-3">Besoin d'aide ?</h5>
                <p class="small mb-4 opacity-75">Gérez vos formations informatiques, suivez la progression de vos apprenants et générez des attestations en un clic.</p>
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.formations.create') }}" class="btn btn-light text-primary fw-bold">
                        <i class="fas fa-plus-circle me-1"></i> Créer une formation
                    </a>
                    <a href="{{ route('admin.attestations.index') }}" class="btn btn-outline-light">
                        <i class="fas fa-certificate me-1"></i> Gérer les attestations
                    </a>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold"><i class="fas fa-calendar-alt text-warning me-2"></i> Évaluations à venir</h6>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @forelse($upcoming_evaluations as $eval)
                        <div class="list-group-item p-3 border-light">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <span class="fw-bold text-dark">{{ $eval->titre }}</span>
                                <span class="badge bg-{{ $eval->type == 'examen' ? 'danger' : 'warning' }} px-2">{{ ucfirst($eval->type) }}</span>
                            </div>
                            <div class="small text-muted mb-2">{{ $eval->formation->nom }}</div>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="small"><i class="far fa-calendar-alt me-1"></i> {{ $eval->date_evaluation->format('d M, Y') }}</div>
                                <div class="small"><i class="far fa-clock me-1"></i> {{ $eval->date_evaluation->format('H:i') }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="p-4 text-center text-muted small">Aucune évaluation prévue</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold"><i class="fas fa-star text-info me-2"></i> Formations Populaires</h6>
            </div>
            <div class="card-body">
                @foreach($top_formations as $f)
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="small fw-bold">{{ $f->nom }}</span>
                            <span class="small text-muted">{{ $f->apprenants_count }} élèves</span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-info" role="progressbar" style="width: {{ ($f->apprenants_count / max(1, $total_apprenants)) * 100 }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
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
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        drawBorder: false,
                        color: 'rgba(0,0,0,0.05)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
});
</script>
@endsection
