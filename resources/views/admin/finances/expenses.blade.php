@extends('layouts.admin')

@section('content')
<div class="row g-4">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm sticky-top" style="top: 20px; z-index: 10;">
            <div class="card-header bg-white py-3 border-0" style="background-color: var(--navbar-bg);border-radius: 10px;">
                <h6 class="mb-0 fw-bold"><i class="fas fa-plus-circle text-danger me-2"></i> Nouvelle Dépense</h6>
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
                    <button type="submit" class="btn btn text-white w-100 fw-bold"style="background-color: var(--navbar-bg);">
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
                            <th class="text-end px-4">Montant</th>
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
                                <td class="text-end px-4 fw-bold text-danger">{{ number_format($d->montant, 0, ',', ' ') }} FCFA</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center py-5 text-muted">Aucune dépense enregistrée</td></tr>
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
@endsection
