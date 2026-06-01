@extends('layouts.admin')

@section('content')
<div class="row g-4">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm sticky-top" style="top: 20px; z-index: 10;">
            <div class="card-header bg-white py-3 border-0" style="background-color: var(--navbar-bg);border-radius: 10px;">
                <h6 class="mb-0 fw-bold text-white"><i class="fas fa-plus-circle text-warning me-2"></i> Nouvelle Dépense</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.finances.expenses.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Titre de la dépense</label>
                        <input type="text" name="titre" class="form-control" placeholder="Ex: Loyer du mois, Salaire formateur..." required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Catégorie</label>
                        <select name="categorie" class="form-select" required>
                            @foreach($categories as $cat)
                                <option value="{{ $cat }}">{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Montant (FCFA)</label>
                        <input type="number" name="montant" class="form-control" placeholder="0" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Date</label>
                        <input type="date" name="date_depense" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Bénéficiaire (Optionnel)</label>
                        <input type="text" name="beneficiaire" class="form-control" placeholder="Nom du fournisseur, employé...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Description</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Détails de l'achat..."></textarea>
                    </div>
                    <button type="submit" class="btn btn text-white w-100 fw-bold" style="background-color: var(--navbar-bg);">
                        <i class="fas fa-save me-1"></i> Enregistrer
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="mb-0 fw-bold"><i class="fas fa-file-invoice-dollar text-danger me-2"></i> Registre des Dépenses</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-uppercase" style="font-size: 0.7rem;">
                        <tr>
                            <th class="px-4">Date</th>
                            <th>Titre / Catégorie</th>
                            <th>Bénéficiaire</th>
                            <th class="text-end">Montant</th>
                            <th class="text-center px-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($depenses as $d)
                            <tr>
                                <td class="px-4 small text-muted">{{ $d->date_depense->format('d/m/Y') }}</td>
                                <td>
                                    <div class="fw-bold">{{ $d->titre }}</div>
                                    <span class="badge bg-light text-dark border small" style="font-size: 0.65rem;">{{ $d->categorie }}</span>
                                </td>
                                <td><small>{{ $d->beneficiaire ?: '-' }}</small></td>
                                <td class="text-end fw-bold text-danger">{{ number_format($d->montant, 0, ',', ' ') }} FCFA</td>
                                <td class="text-center px-4">
                                    <div class="d-flex justify-content-center gap-2">
                                        @if(Auth::user()->isSuperAdmin() || Auth::user()->hasPermission('edit_expense'))
                                            <button type="button" class="btn btn-sm btn-outline-primary btn-edit-expense" 
                                                    data-id="{{ $d->id }}"
                                                    data-titre="{{ $d->titre }}"
                                                    data-categorie="{{ $d->categorie }}"
                                                    data-montant="{{ $d->montant }}"
                                                    data-date_depense="{{ $d->date_depense->format('Y-m-d') }}"
                                                    data-beneficiaire="{{ $d->beneficiaire }}"
                                                    data-description="{{ $d->description }}"
                                                    title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        @endif
                                        @if(Auth::user()->isSuperAdmin() || Auth::user()->hasPermission('delete_expense'))
                                            <form action="{{ route('admin.finances.expenses.destroy', $d) }}" method="POST" class="delete-expense-form d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-outline-danger btn-delete-expense" data-titre="{{ $d->titre }}" title="Supprimer">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center py-5 text-muted">Aucune dépense enregistrée</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($depenses->hasPages())
                <div class="card-footer bg-white">
                    {{ $depenses->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal de modification de dépense -->
<div class="modal fade" id="editExpenseModal" tabindex="-1" aria-labelledby="editExpenseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="editExpenseModalLabel"><i class="fas fa-edit text-warning"></i> Modifier la Dépense</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <form id="editExpenseForm" action="" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Titre de la dépense</label>
                        <input type="text" name="titre" id="edit_titre" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Catégorie</label>
                        <select name="categorie" id="edit_categorie" class="form-select" required>
                            @foreach($categories as $cat)
                                <option value="{{ $cat }}">{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Montant (FCFA)</label>
                        <input type="number" name="montant" id="edit_montant" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Date</label>
                        <input type="date" name="date_depense" id="edit_date_depense" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Bénéficiaire (Optionnel)</label>
                        <input type="text" name="beneficiaire" id="edit_beneficiaire" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Description</label>
                        <textarea name="description" id="edit_description" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
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
    const editModal = new bootstrap.Modal(document.getElementById('editExpenseModal'));
    const editForm = document.getElementById('editExpenseForm');
    
    document.querySelectorAll('.btn-edit-expense').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.dataset.id;
            document.getElementById('edit_titre').value = this.dataset.titre;
            document.getElementById('edit_categorie').value = this.dataset.categorie;
            document.getElementById('edit_montant').value = this.dataset.montant;
            document.getElementById('edit_date_depense').value = this.dataset.date_depense;
            document.getElementById('edit_beneficiaire').value = this.dataset.beneficiaire || '';
            document.getElementById('edit_description').value = this.dataset.description || '';
            
            editForm.action = `/admin/finances/depenses/${id}`;
            editModal.show();
        });
    });

    document.querySelectorAll('.btn-delete-expense').forEach(button => {
        button.addEventListener('click', function() {
            const titre = this.dataset.titre;
            const form = this.closest('form');
            
            Swal.fire({
                title: 'Supprimer la dépense',
                text: `Voulez-vous vraiment supprimer la dépense "${titre}" ?`,
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
