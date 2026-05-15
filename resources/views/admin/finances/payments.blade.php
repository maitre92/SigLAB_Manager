@extends('layouts.admin')

@section('content')
<div class="row g-4">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm sticky-top" style="top: 20px; z-index: 10;">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="mb-0 fw-bold"><i class="fas fa-plus-circle text-primary me-2"></i> Enregistrer un Paiement</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.finances.payments.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Apprenant & Formation</label>
                        <select name="inscription_id" class="form-select" required>
                            <option value="">Sélectionner une inscription...</option>
                            @foreach($inscriptions as $ins)
                                <option value="{{ $ins->id }}">
                                    {{ $ins->apprenant->nom_complet }} - {{ $ins->formation->nom }} 
                                    (Reste: {{ number_format($ins->montant_total - $ins->montant_paye, 0, ',', ' ') }} FCFA)
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Montant (FCFA)</label>
                        <input type="number" name="montant" class="form-control" placeholder="0" required>
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
                <h6 class="mb-0 fw-bold"><i class="fas fa-list text-primary me-2"></i> Historique des Paiements</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-uppercase" style="font-size: 0.7rem;">
                        <tr>
                            <th class="px-4">Reçu / Date</th>
                            <th>Apprenant</th>
                            <th>Mode</th>
                            <th class="text-end">Montant</th>
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
                                    <div class="fw-bold">{{ $p->inscription->apprenant->nom_complet }}</div>
                                    <small class="text-muted">{{ $p->inscription->formation->nom }}</small>
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
                                <td class="text-center px-4">
                                    <a href="{{ route('admin.finances.payments.receipt', $p) }}" target="_blank" class="btn btn-sm btn-light border" title="Imprimer le reçu">
                                        <i class="fas fa-print text-primary"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center py-5 text-muted">Aucun paiement enregistré</td></tr>
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
@endsection
