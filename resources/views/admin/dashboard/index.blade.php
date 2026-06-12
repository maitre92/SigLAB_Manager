@extends('layouts.admin')

@section('content')
@php
    $cards = collect($dashboard_cards ?? []);
    $actions = collect($quick_actions ?? []);
@endphp

<div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1">{{ $dashboard_title ?? 'Tableau de bord' }}</h4>
        <div class="text-muted">{{ $dashboard_subtitle ?? 'Bienvenue dans votre espace de travail.' }}</div>
    </div>
    @if($actions->isNotEmpty())
        <div class="d-flex gap-2 flex-wrap justify-content-end">
            @foreach($actions as $action)
                @can($action['permission'])
                    <a href="{{ route($action['route']) }}" class="btn btn-sm text-white shadow-sm" style="background-color: var(--navbar-bg);">
                        <i class="{{ $action['icon'] }} me-1"></i> {{ $action['label'] }}
                    </a>
                @endcan
            @endforeach
        </div>
    @endif
</div>

<div class="row g-4 mb-4">
    @foreach($cards as $card)
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm stat-card h-100 overflow-hidden" style="background-color: var(--navbar-bg);">
                <div class="card-body p-3 text-white">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="stat-icon-wrapper">
                            <i class="{{ $card['icon'] }}"></i>
                        </div>
                        <span class="badge bg-white bg-opacity-25 rounded-pill small" style="font-size: 0.7rem;">{{ $card['badge'] }}</span>
                    </div>
                    <h6 class="text-white text-opacity-75 mb-1 fw-medium small">{{ $card['label'] }}</h6>
                    <h3 class="fw-bold mb-0 counter-value">{{ $card['value'] }}</h3>
                    <div class="mt-2 progress bg-white bg-opacity-20" style="height: 3px;">
                        <div class="progress-bar bg-white" style="width: 100%"></div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

@if(($dashboard_type ?? 'admin') === 'trainer')
    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-users text-primary me-2"></i> Mes groupes</h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Groupe</th>
                                <th>Formation</th>
                                <th>Statut</th>
                                <th class="text-end">Heures validées</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($trainer_groupes ?? collect() as $groupe)
                                @php
                                    $minutes = $groupe->emargements->where('statut', \App\Models\Emargement::STATUT_VALIDE)->sum('duree_minutes');
                                    $hours = round($minutes / 60, 2);
                                @endphp
                                <tr>
                                    <td class="fw-bold">{{ $groupe->nom }}</td>
                                    <td>{{ $groupe->formation->nom ?? '-' }}</td>
                                    <td><span class="badge bg-light text-dark border">{{ $groupe->statut_label }}</span></td>
                                    <td class="text-end fw-bold">{{ $hours }} / {{ $groupe->formation->duree_heures ?? 0 }} h</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center py-4 text-muted">Aucun groupe affecté.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-signature text-warning me-2"></i> Derniers émargements</h6>
                </div>
                <div class="list-group list-group-flush">
                    @forelse($recent_emargements ?? collect() as $emargement)
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between gap-2">
                                <div>
                                    <div class="fw-bold small">{{ $emargement->titre_realise }}</div>
                                    <div class="small text-muted">{{ $emargement->groupeFormation->nom ?? '-' }} - {{ $emargement->date_seance->format('d/m/Y') }}</div>
                                </div>
                                <span class="badge bg-light text-dark border">{{ ucfirst(str_replace('_', ' ', $emargement->statut)) }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="p-4 text-center text-muted small">Aucun émargement.</div>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-calendar-alt text-primary me-2"></i> Emploi du temps de mes groupes</h6>
                    <a href="{{ route('admin.suivi-pedagogique.index') }}" class="btn btn-sm btn-light border">
                        <i class="fas fa-signature me-1"></i> Émarger
                    </a>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @forelse($trainer_schedules ?? collect() as $item)
                            <div class="col-xl-6">
                                <div class="border rounded p-3 h-100">
                                    <div class="d-flex justify-content-between align-items-start gap-2 mb-3">
                                        <div>
                                            <div class="fw-bold">{{ $item['groupe']->nom }}</div>
                                            <div class="small text-muted">{{ $item['formation']->nom ?? 'Formation non définie' }}</div>
                                        </div>
                                        <span class="badge bg-light text-dark border">{{ $item['groupe']->statut_label }}</span>
                                    </div>

                                    @if(!empty($item['schedule']))
                                        <div class="table-responsive">
                                            <table class="table table-sm align-middle mb-0">
                                                <thead class="bg-light">
                                                    <tr>
                                                        <th>Jour</th>
                                                        <th>Début</th>
                                                        <th>Fin</th>
                                                        <th>Activité</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($item['schedule'] as $slot)
                                                        <tr>
                                                            <td class="fw-bold">{{ $slot['day'] ?: '-' }}</td>
                                                            <td>{{ $slot['start'] ?: '-' }}</td>
                                                            <td>{{ $slot['end'] ?: '-' }}</td>
                                                            <td>{{ $slot['activity'] ?: '-' }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @elseif(!empty($item['plain_schedule']))
                                        <div class="small text-muted border rounded p-2 bg-light">{{ $item['plain_schedule'] }}</div>
                                    @else
                                        <div class="text-center text-muted small py-3">
                                            <i class="fas fa-calendar-times mb-2 d-block" style="font-size: 1.4rem;"></i>
                                            Aucun emploi du temps défini pour ce groupe.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center text-muted py-4">Aucun groupe affecté.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@elseif(($dashboard_type ?? '') === 'accountant')
    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-receipt text-success me-2"></i> Derniers paiements</h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light"><tr><th>Reçu</th><th>Apprenant</th><th>Date</th><th class="text-end">Montant</th></tr></thead>
                        <tbody>
                            @forelse($recent_payments ?? collect() as $payment)
                                <tr>
                                    <td><span class="badge bg-light text-dark border">{{ $payment->recu_numero }}</span></td>
                                    <td>{{ $payment->inscription->apprenant->nom_complet ?? '-' }}</td>
                                    <td>{{ $payment->date_paiement->format('d/m/Y') }}</td>
                                    <td class="text-end fw-bold">{{ number_format((float) $payment->montant, 0, ',', ' ') }} FCFA</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center py-4 text-muted">Aucun paiement.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-file-invoice text-danger me-2"></i> Dernières dépenses</h6>
                </div>
                <div class="list-group list-group-flush">
                    @forelse($recent_expenses ?? collect() as $expense)
                        <div class="list-group-item d-flex justify-content-between gap-2">
                            <div>
                                <div class="fw-bold small">{{ $expense->titre }}</div>
                                <div class="small text-muted">{{ $expense->categorie }} - {{ $expense->date_depense->format('d/m/Y') }}</div>
                            </div>
                            <span class="fw-bold text-danger">{{ number_format((float) $expense->montant, 0, ',', ' ') }}</span>
                        </div>
                    @empty
                        <div class="p-4 text-center text-muted small">Aucune dépense.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@else
    <div class="row g-4">
        <div class="col-lg-8">
            @if(isset($chart_data))
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3 border-0">
                        <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-chart-line text-primary me-2"></i> Flux d'inscriptions (6 mois)</h6>
                    </div>
                    <div class="card-body">
                        <canvas id="inscriptionsChart" height="300"></canvas>
                    </div>
                </div>
            @endif

            @if(isset($recent_apprenants))
                <div class="card border-0 shadow-sm overflow-hidden">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-0">
                        <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-users text-primary me-2"></i> Derniers apprenants</h6>
                        @can('view_learners')
                            <a href="{{ route('admin.apprenants.index') }}" class="btn btn-sm btn-light border">Voir tout</a>
                        @endcan
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light"><tr><th>Apprenant</th><th>Matricule</th><th>Date</th><th class="text-end">Action</th></tr></thead>
                            <tbody>
                                @forelse($recent_apprenants as $app)
                                    <tr>
                                        <td>
                                            <div class="fw-bold text-dark">{{ $app->nom_complet }}</div>
                                            <small class="text-muted">{{ $app->email }}</small>
                                        </td>
                                        <td><span class="badge bg-light text-dark border">{{ $app->matricule }}</span></td>
                                        <td>{{ $app->created_at->format('d/m/Y') }}</td>
                                        <td class="text-end">
                                            @can('view_learner_details')
                                                <a href="{{ route('admin.apprenants.show', $app) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">Détails</a>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center py-4">Aucun apprenant</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-lg-4">
            @if(isset($upcoming_evaluations))
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3 border-0">
                        <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-calendar-check text-warning me-2"></i> Prochaines évaluations</h6>
                    </div>
                    <div class="list-group list-group-flush">
                        @forelse($upcoming_evaluations as $eval)
                            <div class="list-group-item p-3">
                                <div class="fw-bold small">{{ $eval->titre }}</div>
                                <div class="small text-muted">{{ $eval->formation->nom ?? '-' }}</div>
                                <div class="small text-dark mt-1">{{ $eval->date_evaluation->format('d/m/Y H:i') }}</div>
                            </div>
                        @empty
                            <div class="p-4 text-center text-muted small">Aucune évaluation prévue</div>
                        @endforelse
                    </div>
                </div>
            @endif

            @if(isset($top_formations))
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3 border-0">
                        <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-trophy text-info me-2"></i> Formations populaires</h6>
                    </div>
                    <div class="card-body">
                        @forelse($top_formations as $f)
                            <div class="mb-4">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="small fw-bold text-dark">{{ $f->nom }}</span>
                                    <span class="small text-primary fw-bold">{{ $f->apprenants_count }} inscrits</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-primary" style="width: {{ ($f->apprenants_count / max(1, $total_apprenants ?? 1)) * 100 }}%"></div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-muted small">Aucune formation.</div>
                        @endforelse
                    </div>
                </div>
            @endif
        </div>
    </div>
@endif

<style>
    .stat-card {
        transition: all 0.3s cubic-bezier(.25,.8,.25,1);
        border-radius: 8px;
        position: relative;
        z-index: 1;
        border: none !important;
    }

    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    }

    .stat-icon-wrapper {
        width: 40px;
        height: 40px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .counter-value {
        font-size: 1.45rem;
        letter-spacing: 0;
    }

    .table thead th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        color: #6c757d;
        padding: 12px 16px;
    }
</style>
@endsection

@section('js')
@if(isset($chart_data))
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const canvas = document.getElementById('inscriptionsChart');
    if (!canvas) return;

    new Chart(canvas.getContext('2d'), {
        type: 'line',
        data: @json($chart_data),
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 } },
                x: { grid: { display: false } }
            }
        }
    });
});
</script>
@endif
@endsection
