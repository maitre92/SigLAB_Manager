@extends('layouts.admin')

@section('title', 'Paiements')

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
<div class="row g-4">
    <div class="col-lg-4 d-print-none">
        <div class="card border-0 shadow-sm sticky-top" style="top: 20px; z-index: 10;">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="mb-0 fw-bold"><i class="fas fa-plus-circle text-primary me-2"></i> Enregistrer un Paiement</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.finances.payments.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-bold"><i class="fas fa-users text-muted me-1"></i> Groupe</label>
                        <select id="groupe_select" class="form-select" required>
                            <option value="">Sélectionner un groupe...</option>
                            @foreach($inscriptions->pluck('groupeFormation')->filter()->unique('id')->sortBy('nom') as $groupe)
                                <option value="{{ $groupe->id }}">{{ $groupe->nom }} - {{ $groupe->formation->nom ?? 'Formation' }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold"><i class="fas fa-user text-muted me-1"></i> Apprenant</label>
                        <select id="inscription_select" name="inscription_id" class="form-select" required disabled>
                            <option value="">Sélectionner d'abord le groupe...</option>
                        </select>
                    </div>

                    <!-- Résumé Financier -->
                    <div id="financial_summary" class="card border-0 bg-light p-3 mb-3 d-none shadow-none" style="border: 1px dashed var(--card-border) !important; background-color: rgba(64, 96, 160, 0.05) !important;">
                        <h6 class="fw-bold mb-3 text-dark small d-flex align-items-center">
                            <i class="fas fa-wallet text-primary me-2"></i> Résumé Financier
                        </h6>
                        <div class="d-flex justify-content-between mb-2 small">
                            <span class="text-muted">Total Formation :</span>
                            <span id="summary_total" class="fw-bold text-dark">0 FCFA</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2 small">
                            <span class="text-muted">Montant Déjà Payé :</span>
                            <span id="summary_paye" class="fw-bold text-success">0 FCFA</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2 small">
                            <span class="text-muted">Statut Inscription :</span>
                            <span id="summary_status" class="badge">---</span>
                        </div>
                        <hr class="my-2 bg-secondary opacity-25">
                        <div class="d-flex justify-content-between small align-items-center">
                            <span class="text-muted fw-bold">Reste à Payer :</span>
                            <span id="summary_reste" class="fw-bold fs-6 text-danger">0 FCFA</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <label class="form-label small fw-bold mb-0"><i class="fas fa-money-bill-wave text-muted me-1"></i> Montant à payer (FCFA)</label>
                            <button type="button" id="pay_all_btn" class="btn btn-xs py-0 px-2 btn-outline-secondary d-none" style="font-size: 0.75rem;">
                                <i class="fas fa-coins me-1"></i> Tout payer
                            </button>
                        </div>
                        <input type="number" id="montant_input" name="montant" class="form-control mt-1" placeholder="0" min="1" required disabled>
                        <div id="montant_warning" class="text-danger small mt-2 d-none">
                            <i class="fas fa-exclamation-triangle me-1"></i> Le montant dépasse le reste à payer.
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold">Date</label>
                            <input type="date" name="date_paiement" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold">Mode</label>
                            <select name="mode_paiement" class="form-select" required>
                                <option value="espèces">Espèces</option>
                                <option value="wave">Wave</option>
                                <option value="orange_money">Orange Money</option>
                                <option value="virement">Virement</option>
                                <option value="cheque">Chèque</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Référence (Optionnel)</label>
                        <input type="text" name="reference" class="form-control" placeholder="N° transaction, chèque...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Notes</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Commentaire..."></textarea>
                    </div>
                    <button type="submit" class="btn btn text-white w-100 fw-bold"style="background-color: var(--navbar-bg);">
                        <i class="fas fa-save me-1"></i> Valider le paiement
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-0">
                <div class="d-flex flex-column gap-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold"><i class="fas fa-list text-primary me-2"></i> Historique des Paiements</h6>
                        <button onclick="window.print()" class="btn btn-sm btn-outline-secondary d-print-none shadow-sm">
                            <i class="fas fa-print me-1"></i> Imprimer la liste
                        </button>
                    </div>
                    <form method="GET" action="{{ route('admin.finances.payments') }}" class="row g-2 align-items-center">
                        <div class="col-md-4">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                                <input type="text" name="search" class="form-control border-start-0" placeholder="Rechercher..." value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select name="groupe_id" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="">Tous les groupes</option>
                                @foreach($groupes as $g)
                                    <option value="{{ $g->id }}" {{ request('groupe_id') == $g->id ? 'selected' : '' }}>
                                        {{ $g->nom }} {{ $g->code ? '[' . $g->code . ']' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="formation_id" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="">Toutes les formations</option>
                                @foreach($formations as $f)
                                    <option value="{{ $f->id }}" {{ request('formation_id') == $f->id ? 'selected' : '' }}>
                                        {{ $f->nom }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            @if(request()->anyFilled(['search', 'groupe_id', 'formation_id']))
                                <a href="{{ route('admin.finances.payments') }}" class="btn btn-sm btn-outline-secondary w-100">
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
                            <th>Apprenant</th>
                            <th>Mode</th>
                            <th class="text-end">Montant</th>
                            <th class="text-end">Reste à payer</th>
                            <th class="text-center px-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($paiements as $p)
                            <tr>
                                <td class="px-4">
                                    <div class="fw-bold text-dark">{{ $p->recu_numero }}</div>
                                    <small class="text-muted">{{ $p->date_paiement->format('d/m/Y') }}</small>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $p->inscription?->apprenant?->nom_complet ?? 'Apprenant supprimé' }}</div>
                                    <div class="d-flex flex-wrap gap-1 mt-1">
                                        @if($p->inscription?->formation)
                                            <span class="badge bg-light text-secondary border fw-normal" style="font-size: 0.7rem;">
                                                <i class="fas fa-graduation-cap me-1"></i>{{ $p->inscription->formation->nom }}
                                            </span>
                                        @endif
                                        @if($p->inscription?->groupeFormation)
                                            <span class="badge text-white fw-normal" style="font-size: 0.7rem; background-color: var(--navbar-bg); opacity: 0.85;">
                                                <i class="fas fa-users me-1"></i>{{ $p->inscription->groupeFormation->nom }}
                                            </span>
                                        @endif
                                    </div>
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
                                <td class="text-end fw-bold text-dark">
                                    @if($p->inscription)
                                        @if($p->inscription->reste_a_payer <= 0)
                                            <span class="badge bg-success" style="font-size: 0.75rem;">Solder</span>
                                        @else
                                            <span class="text-danger fw-bold" style="font-size: 0.85rem;">{{ number_format($p->inscription->reste_a_payer, 0, ',', ' ') }} FCFA</span>
                                        @endif
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center px-4">
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="{{ route('admin.finances.payments.receipt', $p) }}" target="_blank" class="btn btn-sm btn-outline-secondary" title="Imprimer le reçu">
                                            <i class="fas fa-print"></i>
                                        </a>
                                        @if(Auth::user()->isSuperAdmin() || Auth::user()->hasPermission('edit_payment'))
                                            <button type="button" class="btn btn-sm btn-outline-primary btn-edit-payment"
                                                    data-id="{{ $p->id }}"
                                                    data-montant="{{ (int)$p->montant }}"
                                                    data-date_paiement="{{ $p->date_paiement->format('Y-m-d') }}"
                                                    data-mode_paiement="{{ $p->mode_paiement }}"
                                                    data-reference="{{ $p->reference }}"
                                                    data-notes="{{ $p->notes }}"
                                                    data-apprenant="{{ $p->inscription?->apprenant?->nom_complet ?? 'Apprenant' }}"
                                                    data-formation="{{ $p->inscription?->formation?->nom ?? 'Formation' }}"
                                                    data-max_montant="{{ (int)($p->inscription->montant_total - ($p->inscription->montant_paye - $p->montant)) }}"
                                                    title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        @endif
                                        @if(Auth::user()->isSuperAdmin() || Auth::user()->hasPermission('delete_payment'))
                                            <form action="{{ route('admin.finances.payments.destroy', $p) }}" method="POST" class="delete-payment-form d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-outline-danger btn-delete-payment" 
                                                        data-recu="{{ $p->recu_numero }}" 
                                                        data-apprenant="{{ $p->inscription?->apprenant?->nom_complet ?? 'Apprenant' }}"
                                                        title="Supprimer">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center py-5 text-muted">Aucun paiement enregistré</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($paiements->hasPages())
                <div class="card-footer bg-white">
                    {{ $paiements->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal de modification de paiement -->
<div class="modal fade" id="editPaymentModal" tabindex="-1" aria-labelledby="editPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white" style="background-color: var(--navbar-bg) !important;">
                <h5 class="modal-title" id="editPaymentModalLabel"><i class="fas fa-edit text-warning me-2"></i> Modifier le Paiement</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <form id="editPaymentForm" action="" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <!-- Infos Apprenant / Formation -->
                    <div class="alert alert-info py-2 px-3 mb-3 small" style="border: 1px dashed var(--card-border) !important; background-color: rgba(64, 96, 160, 0.05) !important; color: inherit;">
                        <div class="fw-bold"><i class="fas fa-user text-primary me-1"></i> <span id="edit_info_apprenant" class="text-dark"></span></div>
                        <div class="text-muted mt-1"><i class="fas fa-graduation-cap text-secondary me-1"></i> <span id="edit_info_formation"></span></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Montant (FCFA)</label>
                        <input type="number" name="montant" id="edit_montant" class="form-control" required min="1">
                        <div id="edit_montant_warning" class="text-danger small mt-2 d-none">
                            <i class="fas fa-exclamation-triangle me-1"></i> Le montant dépasse le reste à payer.
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold">Date</label>
                            <input type="date" name="date_paiement" id="edit_date_paiement" class="form-control" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold">Mode</label>
                            <select name="mode_paiement" id="edit_mode_paiement" class="form-select" required>
                                <option value="espèces">Espèces</option>
                                <option value="wave">Wave</option>
                                <option value="orange_money">Orange Money</option>
                                <option value="virement">Virement</option>
                                <option value="cheque">Chèque</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Référence (Optionnel)</label>
                        <input type="text" name="reference" id="edit_reference" class="form-control" placeholder="N° transaction, chèque...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Notes</label>
                        <textarea name="notes" id="edit_notes" class="form-control" rows="3" placeholder="Commentaire..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" id="edit_submit_btn" class="btn btn-primary" style="background-color: var(--navbar-bg);">Enregistrer les modifications</button>
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
    // Les données des inscriptions passées depuis le serveur
    const inscriptions = @json($inscriptions);

    const groupeSelect = document.getElementById('groupe_select');
    const inscriptionSelect = document.getElementById('inscription_select');
    const financialSummary = document.getElementById('financial_summary');
    const summaryTotal = document.getElementById('summary_total');
    const summaryPaye = document.getElementById('summary_paye');
    const summaryStatus = document.getElementById('summary_status');
    const summaryReste = document.getElementById('summary_reste');
    const montantInput = document.getElementById('montant_input');
    const payAllBtn = document.getElementById('pay_all_btn');
    const montantWarning = document.getElementById('montant_warning');
    const submitBtn = document.querySelector('form button[type="submit"]');

    let selectedInscription = null;

    // Formater en monnaie FCFA
    function formatFCFA(value) {
        return new Intl.NumberFormat('fr-FR').format(value) + ' FCFA';
    }

    // Changement de groupe
    groupeSelect.addEventListener('change', function() {
        const groupeId = this.value;
        
        // Vider le select des inscriptions/apprenants
        inscriptionSelect.innerHTML = '<option value="">Sélectionner un apprenant...</option>';
        financialSummary.classList.add('d-none');
        montantInput.disabled = true;
        montantInput.value = '';
        payAllBtn.classList.add('d-none');
        montantWarning.classList.add('d-none');
        submitBtn.disabled = false;
        selectedInscription = null;

        if (!groupeId) {
            inscriptionSelect.disabled = true;
            return;
        }

        // Filtrer les inscriptions pour le groupe sélectionné
        const filtered = inscriptions.filter(ins => ins.groupe_formation_id == groupeId);

        if (filtered.length === 0) {
            inscriptionSelect.innerHTML = '<option value="">Aucun apprenant inscrit à ce groupe</option>';
            inscriptionSelect.disabled = true;
            return;
        }

        filtered.forEach(ins => {
            const text = `${ins.apprenant.prenom} ${ins.apprenant.nom} (Matricule: ${ins.apprenant.matricule})`;
            const option = document.createElement('option');
            option.value = ins.id;
            option.textContent = text;
            inscriptionSelect.appendChild(option);
        });

        inscriptionSelect.disabled = false;
    });

    // Changement d'apprenant (inscription)
    inscriptionSelect.addEventListener('change', function() {
        const insId = this.value;
        montantWarning.classList.add('d-none');
        submitBtn.disabled = false;

        if (!insId) {
            financialSummary.classList.add('d-none');
            montantInput.disabled = true;
            montantInput.value = '';
            payAllBtn.classList.add('d-none');
            selectedInscription = null;
            return;
        }

        // Trouver l'inscription sélectionnée
        selectedInscription = inscriptions.find(ins => ins.id == insId);

        if (selectedInscription) {
            const total = parseFloat(selectedInscription.montant_total);
            const paye = parseFloat(selectedInscription.montant_paye);
            const reste = total - paye;

            // Mettre à jour le résumé financier
            summaryTotal.textContent = formatFCFA(total);
            summaryPaye.textContent = formatFCFA(paye);
            summaryReste.textContent = formatFCFA(reste);

            // Gérer le badge de statut
            summaryStatus.className = 'badge';
            let statusLabel = '';
            if (selectedInscription.statut === 'validee') {
                summaryStatus.classList.add('bg-success');
                statusLabel = 'Validée';
            } else if (selectedInscription.statut === 'en_attente') {
                summaryStatus.classList.add('bg-warning', 'text-dark');
                statusLabel = 'En attente';
            } else if (selectedInscription.statut === 'annulee') {
                summaryStatus.classList.add('bg-danger');
                statusLabel = 'Annulée';
            } else if (selectedInscription.statut === 'terminee') {
                summaryStatus.classList.add('bg-info');
                statusLabel = 'Terminée';
            } else {
                summaryStatus.classList.add('bg-secondary');
                statusLabel = selectedInscription.statut;
            }
            summaryStatus.textContent = statusLabel;

            // Afficher le résumé financier
            financialSummary.classList.remove('d-none');

            // Gérer les contraintes sur le montant de paiement
            if (reste <= 0) {
                montantInput.disabled = true;
                montantInput.value = '0';
                payAllBtn.classList.add('d-none');
                
                montantWarning.classList.remove('d-none');
                montantWarning.className = 'alert alert-info py-2 px-3 mt-2 mb-0 small';
                montantWarning.innerHTML = '<i class="fas fa-check-circle me-1"></i> Cette inscription est déjà entièrement réglée.';
                submitBtn.disabled = true;
            } else {
                montantInput.disabled = false;
                montantInput.max = reste;
                montantInput.min = 1;
                montantInput.value = '';
                
                payAllBtn.classList.remove('d-none');
                payAllBtn.title = `Régler le reste de ${formatFCFA(reste)}`;
            }
        }
    });

    // Clic sur "Tout payer"
    payAllBtn.addEventListener('click', function(e) {
        e.preventDefault();
        if (selectedInscription) {
            const reste = parseFloat(selectedInscription.montant_total) - parseFloat(selectedInscription.montant_paye);
            montantInput.value = reste;
            // Déclencher l'événement input pour la validation
            montantInput.dispatchEvent(new Event('input'));
        }
    });

    // Validation dynamique du montant saisi
    montantInput.addEventListener('input', function() {
        if (!selectedInscription) return;

        const val = parseFloat(this.value) || 0;
        const reste = parseFloat(selectedInscription.montant_total) - parseFloat(selectedInscription.montant_paye);

        if (val > reste) {
            montantWarning.classList.remove('d-none');
            montantWarning.className = 'text-danger small mt-2';
            montantWarning.innerHTML = `<i class="fas fa-exclamation-triangle me-1"></i> Le montant saisi (${formatFCFA(val)}) dépasse le reste à payer (${formatFCFA(reste)}).`;
            submitBtn.disabled = true;
        } else if (val <= 0) {
            montantWarning.classList.remove('d-none');
            montantWarning.className = 'text-danger small mt-2';
            montantWarning.innerHTML = `<i class="fas fa-exclamation-triangle me-1"></i> Le montant doit être supérieur à 0.`;
            submitBtn.disabled = true;
        } else {
            montantWarning.classList.add('d-none');
            submitBtn.disabled = false;
        }
    });

    // Modal de modification
    const editModal = new bootstrap.Modal(document.getElementById('editPaymentModal'));
    const editForm = document.getElementById('editPaymentForm');
    const editMontant = document.getElementById('edit_montant');
    const editMontantWarning = document.getElementById('edit_montant_warning');
    const editSubmitBtn = document.getElementById('edit_submit_btn');
    let currentMaxMontant = 0;

    document.querySelectorAll('.btn-edit-payment').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.dataset.id;
            const montant = this.dataset.montant;
            const date = this.dataset.date_paiement;
            const mode = this.dataset.mode_paiement;
            const reference = this.dataset.reference || '';
            const notes = this.dataset.notes || '';
            const apprenant = this.dataset.apprenant;
            const formation = this.dataset.formation;
            currentMaxMontant = parseFloat(this.dataset.max_montant);

            document.getElementById('edit_info_apprenant').textContent = apprenant;
            document.getElementById('edit_info_formation').textContent = formation;
            editMontant.value = montant;
            editMontant.max = currentMaxMontant;
            document.getElementById('edit_date_paiement').value = date;
            document.getElementById('edit_mode_paiement').value = mode;
            document.getElementById('edit_reference').value = reference;
            document.getElementById('edit_notes').value = notes;

            editMontantWarning.classList.add('d-none');
            editSubmitBtn.disabled = false;

            editForm.action = `/admin/finances/paiements/${id}`;
            editModal.show();
        });
    });

    editMontant.addEventListener('input', function() {
        const val = parseFloat(this.value) || 0;
        if (val > currentMaxMontant) {
            editMontantWarning.classList.remove('d-none');
            editMontantWarning.innerHTML = `<i class="fas fa-exclamation-triangle me-1"></i> Le montant saisi (${formatFCFA(val)}) dépasse le reste à payer maximum autorisé (${formatFCFA(currentMaxMontant)}).`;
            editSubmitBtn.disabled = true;
        } else if (val <= 0) {
            editMontantWarning.classList.remove('d-none');
            editMontantWarning.innerHTML = `<i class="fas fa-exclamation-triangle me-1"></i> Le montant doit être supérieur à 0.`;
            editSubmitBtn.disabled = true;
        } else {
            editMontantWarning.classList.add('d-none');
            editSubmitBtn.disabled = false;
        }
    });

    // Confirmation de suppression
    document.querySelectorAll('.btn-delete-payment').forEach(button => {
        button.addEventListener('click', function() {
            const recu = this.dataset.recu;
            const apprenant = this.dataset.apprenant;
            const form = this.closest('form');
            
            Swal.fire({
                title: 'Supprimer le paiement',
                text: `Voulez-vous vraiment supprimer le paiement de reçu N° "${recu}" pour l'apprenant "${apprenant}" ? Cette action réajustera le reste à payer de l'apprenant.`,
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
