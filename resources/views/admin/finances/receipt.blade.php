<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reçu de Paiement - {{ $paiement->recu_numero }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f8fafc; font-family: 'Inter', sans-serif; }
        .receipt-container { max-width: 800px; margin: 40px auto; background: white; padding: 40px; box-shadow: 0 0 20px rgba(0,0,0,0.05); border-radius: 8px; position: relative; overflow: hidden; }
        .receipt-header { border-bottom: 2px solid #f1f5f9; padding-bottom: 20px; margin-bottom: 30px; }
        .logo-text { font-size: 24px; font-weight: 800; color: #4e54c8; letter-spacing: -1px; }
        .watermark { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(-45deg); font-size: 100px; font-weight: 900; color: rgba(0,0,0,0.03); white-space: nowrap; pointer-events: none; text-transform: uppercase; }
        .label { color: #64748b; font-size: 12px; font-weight: 600; text-transform: uppercase; margin-bottom: 4px; }
        .value { color: #1e293b; font-weight: 700; font-size: 16px; }
        .amount-box { background: #f8fafc; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0; text-align: center; }
        .amount-value { font-size: 32px; font-weight: 800; color: #4e54c8; }
        .footer-note { font-size: 12px; color: #94a3b8; text-align: center; margin-top: 50px; border-top: 1px solid #f1f5f9; pt: 20px; }
        @media print {
            body { background: white; margin: 0; }
            .receipt-container { box-shadow: none; margin: 0; width: 100%; max-width: 100%; border: none; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print text-center mt-4">
        <button onclick="window.print()" class="btn btn-primary px-4 fw-bold">
            <i class="fas fa-print me-2"></i> Imprimer le reçu
        </button>
        <a href="{{ route('admin.finances.payments') }}" class="btn btn-light px-4 border ms-2">Retour</a>
    </div>

    <div class="receipt-container">
        <div class="watermark">PAYÉ / VALIDÉ</div>
        
        <div class="receipt-header d-flex justify-content-between align-items-center">
            <div>
                <div class="logo-text">sigLAB <span class="text-dark">Manager</span></div>
                <div class="small text-muted">Centre de Formation Informatique</div>
            </div>
            <div class="text-end">
                <h4 class="fw-bold mb-0">REÇU DE PAIEMENT</h4>
                <div class="text-primary fw-bold">{{ $paiement->recu_numero }}</div>
            </div>
        </div>

        <div class="row mb-5">
            <div class="col-6">
                <div class="label">Date de Paiement</div>
                <div class="value">{{ $paiement->date_paiement->format('d F Y') }}</div>
            </div>
            <div class="col-6 text-end">
                <div class="label">Mode de Règlement</div>
                <div class="value">{{ ucfirst($paiement->mode_paiement) }}</div>
            </div>
        </div>

        <div class="card border-0 bg-light mb-4">
            <div class="card-body p-4">
                <div class="row g-4">
                    <div class="col-12">
                        <div class="label">Apprenant</div>
                        <div class="value" style="font-size: 20px;">{{ $paiement->inscription->apprenant->nom_complet }}</div>
                        <div class="small text-muted">{{ $paiement->inscription->apprenant->email }}</div>
                    </div>
                    <div class="col-12">
                        <div class="label">Formation concernée</div>
                        <div class="value text-primary">{{ $paiement->inscription->formation->nom }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row align-items-center mb-4">
            <div class="col-7">
                <table class="table table-borderless mb-0">
                    <tr>
                        <td class="ps-0 label py-1">Coût Formation</td>
                        <td class="text-end fw-bold py-1">{{ number_format($paiement->inscription->montant_total, 0, ',', ' ') }} FCFA</td>
                    </tr>
                    <tr>
                        <td class="ps-0 label py-1">Total déjà payé</td>
                        <td class="text-end fw-bold py-1">{{ number_format($paiement->inscription->montant_paye - $paiement->montant, 0, ',', ' ') }} FCFA</td>
                    </tr>
                    <tr class="border-top">
                        <td class="ps-0 label py-2">Versement actuel</td>
                        <td class="text-end fw-bold text-success py-2" style="font-size: 1.1rem;">+ {{ number_format($paiement->montant, 0, ',', ' ') }} FCFA</td>
                    </tr>
                    <tr class="border-top">
                        <td class="ps-0 label py-2">Reste à payer</td>
                        <td class="text-end fw-bold text-danger py-2">{{ number_format($paiement->inscription->montant_total - $paiement->inscription->montant_paye, 0, ',', ' ') }} FCFA</td>
                    </tr>
                </table>
            </div>
            <div class="col-5">
                <div class="amount-box">
                    <div class="label mb-2">Montant Versé</div>
                    <div class="amount-value">{{ number_format($paiement->montant, 0, ',', ' ') }}</div>
                    <div class="fw-bold text-uppercase small">Francs CFA</div>
                </div>
            </div>
        </div>

        @if($paiement->notes)
            <div class="mb-4">
                <div class="label">Observations</div>
                <p class="small text-muted">{{ $paiement->notes }}</p>
            </div>
        @endif

        <div class="row mt-5 pt-4">
            <div class="col-6">
                <div class="text-center">
                    <div class="label mb-4 pb-4">Signature Apprenant</div>
                    <div style="height: 1px; width: 150px; background: #e2e8f0; margin: 0 auto;"></div>
                </div>
            </div>
            <div class="col-6">
                <div class="text-center">
                    <div class="label mb-4">Cachet & Signature Administration</div>
                    <div class="small fw-bold">{{ $paiement->creator->name ?? 'Responsable Administratif' }}</div>
                    <div style="height: 1px; width: 150px; background: #e2e8f0; margin: 10px auto 0;"></div>
                </div>
            </div>
        </div>

        <div class="footer-note">
            Ce reçu est généré électroniquement et constitue une preuve de paiement.<br>
            <strong>sigLAB Manager</strong> - Solution de gestion pour centres de formation.
        </div>
    </div>
</body>
</html>
