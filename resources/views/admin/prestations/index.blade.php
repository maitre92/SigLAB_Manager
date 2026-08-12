@extends('layouts.admin')

@section('title', 'Prestations de Services')

@section('content')
<style>
    @media print {
        .sidebar, .navbar, footer, .d-print-none, .card-footer, #sidebarOverlay, .page-header {
            display: none !important;
        }
        .main-content {
            margin-left: 0 !important;
            padding: 0 !important;
            width: 100% !important;
        }
        .col-lg-8 {
            width: 100% !important;
            flex: 0 0 100% !important;
            max-width: 100% !important;
        }
        .card {
            border: none !important;
            box-shadow: none !important;
        }
        .container-fluid {
            padding: 0 !important;
        }
        table th:last-child, table td:last-child {
            display: none !important;
        }
    }
</style>

{{-- Statistiques Caisse Prestations --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background: linear-gradient(135deg, #6366f1, #8b5cf6); flex-shrink: 0;">
                    <i class="fas fa-cash-register text-white"></i>
                </div>
                <div>
                    <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.7rem;">Caisse Prestations</div>
                    <div class="fw-bold fs-5" style="color: var(--navbar-bg);">{{ number_format($total_prestations, 0, ',', ' ') }} <small class="text-muted" style="font-size: 0.7rem;">FCFA</small></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background: linear-gradient(135deg, #10b981, #34d399); flex-shrink: 0;">
                    <i class="fas fa-calendar-check text-white"></i>
                </div>
                <div>
                    <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.7rem;">Ce mois</div>
                    <div class="fw-bold fs-5 text-success">{{ number_format($total_mois, 0, ',', ' ') }} <small class="text-muted" style="font-size: 0.7rem;">FCFA</small></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background: linear-gradient(135deg, #f59e0b, #fbbf24); flex-shrink: 0;">
                    <i class="fas fa-tools text-white"></i>
                </div>
                <div>
                    <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.7rem;">Total Prestations</div>
                    <div class="fw-bold fs-5">{{ $count_prestations }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background: linear-gradient(135deg, #ec4899, #f472b6); flex-shrink: 0;">
                    <i class="fas fa-chart-line text-white"></i>
                </div>
                <div>
                    <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.7rem;">Ce mois (nb)</div>
                    <div class="fw-bold fs-5">{{ $count_mois }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- Formulaire d'enregistrement --}}
    <div class="col-lg-4 d-print-none">
        <div class="card border-0 shadow-sm sticky-top" style="top: 20px; z-index: 10;">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="mb-0 fw-bold"><i class="fas fa-plus-circle text-primary me-2"></i> Nouvelle Prestation</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.prestations.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-bold"><i class="fas fa-wrench text-muted me-1"></i> Type de prestation</label>
                        <input type="text" name="titre" class="form-control" placeholder="Ex: Réparation ordinateur, Installation logiciel..." value="{{ old('titre') }}" required>
                    </div>

                    <div class="row mb-3">
                        <div class="col-7">
                            <label class="form-label small fw-bold"><i class="fas fa-user text-muted me-1"></i> Nom du client</label>
                            <input type="text" name="client_nom" class="form-control" placeholder="Nom complet du client" value="{{ old('client_nom') }}" required>
                        </div>
                        <div class="col-5">
                            <label class="form-label small fw-bold"><i class="fas fa-phone text-muted me-1"></i> Téléphone</label>
                            <input type="text" name="client_telephone" class="form-control" placeholder="+223..." value="{{ old('client_telephone') }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold"><i class="fas fa-align-left text-muted me-1"></i> Description du travail</label>
                        <textarea name="description" class="form-control" rows="2" placeholder="Détails de la prestation effectuée...">{{ old('description') }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold"><i class="fas fa-money-bill-wave text-muted me-1"></i> Montant (FCFA)</label>
                        <input type="number" name="montant" class="form-control" placeholder="0" min="1" value="{{ old('montant') }}" required>
                    </div>

                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold">Date</label>
                            <input type="date" name="date_prestation" class="form-control" value="{{ old('date_prestation', date('Y-m-d')) }}" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold">Mode de paiement</label>
                            <select name="mode_paiement" class="form-select" required>
                                <option value="espèces" {{ old('mode_paiement') == 'espèces' ? 'selected' : '' }}>Espèces</option>
                                <option value="wave" {{ old('mode_paiement') == 'wave' ? 'selected' : '' }}>Wave</option>
                                <option value="orange_money" {{ old('mode_paiement') == 'orange_money' ? 'selected' : '' }}>Orange Money</option>
                                <option value="virement" {{ old('mode_paiement') == 'virement' ? 'selected' : '' }}>Virement</option>
                                <option value="cheque" {{ old('mode_paiement') == 'cheque' ? 'selected' : '' }}>Chèque</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold">Statut</label>
                            <select name="statut" class="form-select">
                                <option value="terminee" {{ old('statut') == 'terminee' ? 'selected' : '' }}>Terminée</option>
                                <option value="en_cours" {{ old('statut') == 'en_cours' ? 'selected' : '' }}>En cours</option>
                                <option value="livree" {{ old('statut') == 'livree' ? 'selected' : '' }}>Livrée</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold">Référence</label>
                            <input type="text" name="reference" class="form-control" placeholder="N° transaction..." value="{{ old('reference') }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Notes</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Commentaire...">{{ old('notes') }}</textarea>
                    </div>

                    <button type="submit" class="btn text-white w-100 fw-bold" style="background-color: var(--navbar-bg);">
                        <i class="fas fa-save me-1"></i> Enregistrer la prestation
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Liste des prestations --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-0">
                <div class="d-flex flex-column gap-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold"><i class="fas fa-list text-primary me-2"></i> Historique des Prestations</h6>
                        <button onclick="window.print()" class="btn btn-sm btn-outline-secondary d-print-none shadow-sm">
                            <i class="fas fa-print me-1"></i> Imprimer la liste
                        </button>
                    </div>
                    <form method="GET" action="{{ route('admin.prestations.index') }}" class="row g-2 align-items-center">
                        <div class="col-md-4">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                                <input type="text" name="search" class="form-control border-start-0" placeholder="Rechercher..." value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select name="statut" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="">Tous les statuts</option>
                                @foreach(\App\Models\Prestation::getStatuts() as $key => $label)
                                    <option value="{{ $key }}" {{ request('statut') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="date" name="date_debut" class="form-control form-control-sm" value="{{ request('date_debut') }}" placeholder="Date début" onchange="this.form.submit()">
                        </div>
                        <div class="col-md-2">
                            @if(request()->anyFilled(['search', 'statut', 'date_debut', 'date_fin']))
                                <a href="{{ route('admin.prestations.index') }}" class="btn btn-sm btn-outline-secondary w-100">
                                    <i class="fas fa-times me-1"></i> Vider
                                </a>
                            @else
                                <button type="submit" class="btn btn-sm text-white w-100" style="background-color: var(--navbar-bg);">
                                    <i class="fas fa-filter me-1"></i> Filtrer
                                </button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-uppercase" style="font-size: 0.7rem;">
                        <tr>
                            <th class="px-4">Reçu / Date</th>
                            <th>Prestation</th>
                            <th>Client</th>
                            <th>Mode</th>
                            <th class="text-end">Montant</th>
                            <th class="text-center">Statut</th>
                            <th class="text-center px-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($prestations as $p)
                            <tr>
                                <td class="px-4">
                                    <div class="fw-bold text-dark" style="font-size: 0.85rem;">{{ $p->recu_numero }}</div>
                                    <small class="text-muted">{{ $p->date_prestation->format('d/m/Y') }}</small>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $p->titre }}</div>
                                    @if($p->description)
                                        <div class="text-muted small" style="font-size: 0.75rem; max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                            {{ Str::limit($p->description, 50) }}
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-bold text-dark small">{{ $p->client_nom }}</div>
                                    @if($p->client_telephone)
                                        <div class="text-muted" style="font-size: 0.7rem;">
                                            <i class="fas fa-phone me-1"></i>{{ $p->client_telephone }}
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge rounded-pill bg-light text-dark border">{{ ucfirst($p->mode_paiement) }}</span>
                                    @if($p->reference)
                                        <div class="text-muted small" style="font-size: 0.7rem;">Ref: {{ $p->reference }}</div>
                                    @endif
                                </td>
                                <td class="text-end fw-bold text-success">
                                    {{ number_format($p->montant, 0, ',', ' ') }} FCFA
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $p->statut_color }}">{{ $p->statut_label }}</span>
                                </td>
                                <td class="text-center px-4">
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="{{ route('admin.prestations.receipt', $p) }}" target="_blank" class="btn btn-sm btn-outline-secondary" title="Imprimer le reçu">
                                            <i class="fas fa-print"></i>
                                        </a>
                                        @if(Auth::user()->isSuperAdmin() || Auth::user()->hasPermission('edit_prestation'))
                                            <button type="button" class="btn btn-sm btn-outline-primary btn-edit-prestation"
                                                    data-id="{{ $p->id }}"
                                                    data-titre="{{ $p->titre }}"
                                                    data-client_nom="{{ $p->client_nom }}"
                                                    data-client_telephone="{{ $p->client_telephone }}"
                                                    data-description="{{ $p->description }}"
                                                    data-montant="{{ (int)$p->montant }}"
                                                    data-date_prestation="{{ $p->date_prestation->format('Y-m-d') }}"
                                                    data-mode_paiement="{{ $p->mode_paiement }}"
                                                    data-reference="{{ $p->reference }}"
                                                    data-statut="{{ $p->statut }}"
                                                    data-notes="{{ $p->notes }}"
                                                    title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        @endif
                                        @if(Auth::user()->isSuperAdmin() || Auth::user()->hasPermission('delete_prestation'))
                                            <form action="{{ route('admin.prestations.destroy', $p) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-outline-danger btn-delete-prestation"
                                                        data-recu="{{ $p->recu_numero }}"
                                                        data-client="{{ $p->client_nom }}"
                                                        title="Supprimer">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center py-5 text-muted">
                                <i class="fas fa-tools fa-2x mb-3 d-block opacity-25"></i>
                                Aucune prestation enregistrée
                            </td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($prestations->hasPages())
                <div class="card-footer bg-white">
                    {{ $prestations->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Modal de modification de prestation --}}
<div class="modal fade" id="editPrestationModal" tabindex="-1" aria-labelledby="editPrestationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header text-white" style="background-color: var(--navbar-bg) !important;">
                <h5 class="modal-title" id="editPrestationModalLabel"><i class="fas fa-edit text-warning me-2"></i> Modifier la Prestation</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <form id="editPrestationForm" action="" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-12">
                            <label class="form-label small fw-bold">Type de prestation</label>
                            <input type="text" name="titre" id="edit_titre" class="form-control" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-7">
                            <label class="form-label small fw-bold">Nom du client</label>
                            <input type="text" name="client_nom" id="edit_client_nom" class="form-control" required>
                        </div>
                        <div class="col-5">
                            <label class="form-label small fw-bold">Téléphone</label>
                            <input type="text" name="client_telephone" id="edit_client_telephone" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Description</label>
                        <textarea name="description" id="edit_description" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="row mb-3">
                        <div class="col-4">
                            <label class="form-label small fw-bold">Montant (FCFA)</label>
                            <input type="number" name="montant" id="edit_montant" class="form-control" required min="1">
                        </div>
                        <div class="col-4">
                            <label class="form-label small fw-bold">Date</label>
                            <input type="date" name="date_prestation" id="edit_date_prestation" class="form-control" required>
                        </div>
                        <div class="col-4">
                            <label class="form-label small fw-bold">Mode de paiement</label>
                            <select name="mode_paiement" id="edit_mode_paiement" class="form-select" required>
                                <option value="espèces">Espèces</option>
                                <option value="wave">Wave</option>
                                <option value="orange_money">Orange Money</option>
                                <option value="virement">Virement</option>
                                <option value="cheque">Chèque</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold">Statut</label>
                            <select name="statut" id="edit_statut" class="form-select">
                                <option value="en_cours">En cours</option>
                                <option value="terminee">Terminée</option>
                                <option value="livree">Livrée</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold">Référence</label>
                            <input type="text" name="reference" id="edit_reference" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Notes</label>
                        <textarea name="notes" id="edit_notes" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary" style="background-color: var(--navbar-bg);">Enregistrer les modifications</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Modal de modification
    const editModal = new bootstrap.Modal(document.getElementById('editPrestationModal'));
    const editForm = document.getElementById('editPrestationForm');

    document.querySelectorAll('.btn-edit-prestation').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.dataset.id;
            
            document.getElementById('edit_titre').value = this.dataset.titre;
            document.getElementById('edit_client_nom').value = this.dataset.client_nom;
            document.getElementById('edit_client_telephone').value = this.dataset.client_telephone || '';
            document.getElementById('edit_description').value = this.dataset.description || '';
            document.getElementById('edit_montant').value = this.dataset.montant;
            document.getElementById('edit_date_prestation').value = this.dataset.date_prestation;
            document.getElementById('edit_mode_paiement').value = this.dataset.mode_paiement;
            document.getElementById('edit_reference').value = this.dataset.reference || '';
            document.getElementById('edit_statut').value = this.dataset.statut;
            document.getElementById('edit_notes').value = this.dataset.notes || '';

            editForm.action = `/admin/prestations/${id}`;
            editModal.show();
        });
    });

    // Confirmation de suppression
    document.querySelectorAll('.btn-delete-prestation').forEach(button => {
        button.addEventListener('click', function() {
            const recu = this.dataset.recu;
            const client = this.dataset.client;
            const form = this.closest('form');
            
            Swal.fire({
                title: 'Supprimer la prestation',
                text: `Voulez-vous vraiment supprimer la prestation N° "${recu}" pour le client "${client}" ?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Oui, supprimer',
                cancelButtonText: 'Annuler',
                customClass: {
                    confirmButton: 'btn btn-danger me-2',
                    cancelButton: 'btn btn-secondary'
                },
                buttonsStyling: false
            }).then(result => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
});
</script>
@endsection
