<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reçu de Paiement - {{ $paiement->recu_numero }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --accent: #f43f5e;
            --slate-50: #f8fafc;
            --slate-100: #f1f5f9;
            --slate-200: #e2e8f0;
            --slate-700: #334155;
            --slate-900: #0f172a;
        }

        body { 
            background-color: var(--slate-100); 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            color: var(--slate-900);
            margin: 0;
            padding: 0;
            -webkit-print-color-adjust: exact;
        }

        /* Container for the two halves */
        .page-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Each receipt half */
        .receipt-half {
            background: white;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            margin-bottom: 20px;
            position: relative;
            overflow: hidden;
            border: 1px solid var(--slate-200);
            /* Height for 1/2 A4 approximately */
            min-height: 13.8cm; 
        }

        .receipt-half:last-child {
            margin-bottom: 0;
        }

        /* Cut line indicator for web view */
        .cut-line {
            border-top: 2px dashed var(--slate-300);
            margin: 20px 0;
            position: relative;
            text-align: center;
        }
        .cut-line i {
            position: absolute;
            top: -12px;
            left: 50%;
            transform: translateX(-50%);
            background: var(--slate-100);
            padding: 0 10px;
            color: var(--slate-400);
            font-size: 18px;
        }

        /* Decorative Background */
        .receipt-half::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, var(--primary), var(--accent));
        }

        .logo-text { 
            font-family: 'Outfit', sans-serif;
            font-size: 22px; 
            font-weight: 800; 
            margin-bottom: 0;
        }
        .logo-text span { color: var(--primary); }

        .receipt-type {
            font-size: 10px;
            font-weight: 800;
            color: var(--primary);
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }

        .receipt-number {
            font-size: 18px;
            font-weight: 800;
        }

        .section-label { 
            color: var(--slate-700); 
            font-size: 9px; 
            font-weight: 700; 
            text-transform: uppercase; 
            letter-spacing: 0.5px;
            margin-bottom: 4px; 
            display: block;
        }

        .info-value { font-weight: 600; font-size: 13px; }

        .apprenant-box {
            background: var(--slate-50);
            border-radius: 12px;
            padding: 12px;
            border: 1px solid var(--slate-200);
            margin: 10px 0;
        }

        .apprenant-name {
            font-size: 15px;
            font-weight: 800;
            margin-bottom: 2px;
        }

        .amount-card {
            background: var(--slate-900);
            border-radius: 12px;
            padding: 12px;
            color: white;
            text-align: center;
        }

        .amount-val {
            font-size: 24px;
            font-weight: 800;
            font-family: 'Outfit', sans-serif;
            line-height: 1;
        }

        .summary-table { width: 100%; font-size: 11px; }
        .summary-table td { padding: 5px 0; border-bottom: 1px solid var(--slate-100); }
        .summary-total { color: var(--primary); font-weight: 800; font-size: 13px; }

        .signature-area { margin-top: 20px; }
        .signature-box { text-align: center; }
        .signature-line { width: 120px; height: 1px; background: var(--slate-200); margin: 8px auto; }

        .footer {
            margin-top: 15px;
            text-align: center;
            font-size: 8px;
            color: var(--slate-700);
        }

        .no-print-controls {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }

        @media print {
            @page {
                size: A4;
                margin: 0;
            }
            body { 
                background: white; 
                margin: 0; 
                padding: 0; 
                width: 210mm;
                height: 297mm;
            }
            .page-container { 
                padding: 0; 
                max-width: 100%; 
                margin: 0;
            }
            .receipt-half { 
                box-shadow: none !important; 
                border-radius: 0 !important; 
                border: none !important;
                border-bottom: 1px dashed #ccc !important;
                margin-bottom: 0 !important;
                height: 148mm; /* Slightly less than 148.5mm to be safe */
                width: 210mm;
                box-sizing: border-box;
                padding: 20mm !important; /* Proper padding for print */
                overflow: hidden;
            }
            .receipt-half:last-child {
                border-bottom: none !important;
            }
            .cut-line { display: none !important; }
            .no-print-controls { display: none !important; }
        }
    </style>
</head>
<body>
    <div class="no-print-controls">
        <button onclick="window.print()" class="btn btn-primary shadow-sm rounded-pill px-4 fw-bold">
            <i class="fas fa-print me-2"></i> Imprimer
        </button>
        <a href="{{ route('admin.finances.payments') }}" class="btn btn-light shadow-sm rounded-pill px-4 border ms-2">
            Retour
        </a>
    </div>

    <div class="page-container">
        @php $copies = ['COPIE ÉLÈVE', 'COPIE ADMINISTRATION']; @endphp
        
        @foreach($copies as $index => $copy)
            <div class="receipt-half">
                <!-- Header -->
                <div class="row align-items-center mb-3">
                    <div class="col-7">
                        <div class="d-flex align-items-center gap-2">
                            <div class="bg-primary text-white rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 14px;">
                                <i class="fas fa-laptop-code"></i>
                            </div>
                            <h2 class="logo-text">sigLAB <span>Manager</span></h2>
                        </div>
                    </div>
                    <div class="col-5 text-end">
                        <div class="receipt-type">{{ $copy }}</div>
                        <div class="receipt-number text-primary small">{{ $paiement->recu_numero }}</div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-6">
                        <span class="section-label">Date de paiement</span>
                        <div class="info-value">{{ $paiement->date_paiement->format('d/m/Y') }}</div>
                    </div>
                    <div class="col-6 text-end">
                        <span class="section-label">Mode de règlement</span>
                        <div class="info-value">{{ ucfirst($paiement->mode_paiement) }}</div>
                    </div>
                </div>

                <!-- Apprenant -->
                <div class="apprenant-box">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <span class="section-label">Apprenant</span>
                            <div class="apprenant-name">{{ $paiement->inscription->apprenant->nom_complet }}</div>
                            <div class="small text-muted fw-bold">{{ $paiement->inscription->formation->nom }}</div>
                        </div>
                        <div class="col-4">
                            <div class="amount-card">
                                <div class="amount-val">{{ number_format($paiement->montant, 0, ',', ' ') }}</div>
                                <div style="font-size: 8px; font-weight: 700; opacity: 0.8; letter-spacing: 1px;">FCFA</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Summary -->
                <div class="row g-4">
                    <div class="col-7">
                        <table class="summary-table">
                            <tr>
                                <td class="text-muted">Total Formation</td>
                                <td class="text-end fw-bold">{{ number_format($paiement->inscription->montant_total, 0, ',', ' ') }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Déjà payé</td>
                                <td class="text-end fw-bold">{{ number_format($paiement->inscription->montant_paye - $paiement->montant, 0, ',', ' ') }}</td>
                            </tr>
                            <tr>
                                <td class="text-success fw-bold">Versement du jour</td>
                                <td class="text-end text-success fw-bold">+ {{ number_format($paiement->montant, 0, ',', ' ') }}</td>
                            </tr>
                            <tr>
                                <td class="summary-total">Reste à payer</td>
                                <td class="text-end summary-total text-danger">
                                    {{ number_format($paiement->inscription->montant_total - $paiement->inscription->montant_paye, 0, ',', ' ') }}
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-5">
                        @if($paiement->notes)
                            <div class="p-2 border rounded-3 bg-light" style="font-size: 10px; height: 100%;">
                                <span class="section-label">Notes</span>
                                <div class="italic text-muted">"{{ Str::limit($paiement->notes, 80) }}"</div>
                            </div>
                        @else
                            <div class="d-flex align-items-center justify-content-center h-100 border rounded-3 border-dashed text-muted small">
                                <i class="fas fa-certificate me-1"></i> Reçu officiel
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Signatures -->
                <div class="signature-area row mt-4">
                    <div class="col-6">
                        <div class="signature-box">
                            <span class="section-label">Signature Élève</span>
                            <div class="signature-line"></div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="signature-box">
                            <span class="section-label">Administration</span>
                            <div class="fw-bold small text-primary">{{ $paiement->creator->name ?? 'Responsable' }}</div>
                            <div class="signature-line"></div>
                        </div>
                    </div>
                </div>

                <div class="footer">
                    <strong>sigLAB Manager</strong> - Solution de gestion pour centres de formation informatique
                </div>
            </div>

            @if($index == 0)
                <div class="cut-line">
                    <i class="fas fa-cut"></i>
                </div>
            @endif
        @endforeach
    </div>
</body>
</html>