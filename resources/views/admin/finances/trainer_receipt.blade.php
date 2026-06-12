<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reçu de Rémunération - {{ $depense->trainer->name ?? $depense->beneficiaire }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #fd7e14; /* Orange/amber theme for trainers/expenses */
            --primary-dark: #e8590c;
            --accent: #228be6;
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
            font-size: 16px;
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

        .trainer-box {
            background: var(--slate-50);
            border-radius: 12px;
            padding: 12px;
            border: 1px solid var(--slate-200);
            margin: 10px 0;
        }

        .trainer-name {
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
                height: 148mm; 
                width: 210mm;
                box-sizing: border-box;
                padding: 20mm !important; 
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
        <button onclick="window.print()" class="btn btn-warning text-white shadow-sm rounded-pill px-4 fw-bold">
            <i class="fas fa-print me-2"></i> Imprimer
        </button>
        <a href="{{ route('admin.finances.trainer_payments') }}" class="btn btn-light shadow-sm rounded-pill px-4 border ms-2">
            Retour
        </a>
    </div>

    <div class="page-container">
        @php $copies = ['COPIE FORMATEUR', 'COPIE ADMINISTRATION']; @endphp
        
        @foreach($copies as $index => $copy)
            <div class="receipt-half">
                <!-- Header -->
                <div class="row align-items-center mb-3">
                    <div class="col-7">
                        <div class="d-flex align-items-center gap-3">
                            <img src="{{ asset('images/siglab_img2.jpeg') }}" alt="sigLAB Logo" style="height: 70px; width: auto; object-fit: contain; border-radius: 6px;">
                            <div>
                                <h2 class="logo-text" style="font-size: 1.5rem; font-weight: 800; letter-spacing: -0.5px; margin: 0; line-height: 1.1;">SigLAB</h2>
                                <small class="text-muted" style="font-size: 9px; display: block; margin-top: 2px; font-weight: 500; line-height: 1.2;">
                                    Technologie SARL<br>
                                    Tél: +223 93 38 73 25
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="col-5 text-end">
                        <div class="receipt-type">{{ $copy }}</div>
                        <div class="receipt-number text-warning small">REC-TRAIN-{{ str_pad($depense->id, 5, '0', STR_PAD_LEFT) }}</div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-6">
                        <span class="section-label">Date de versement</span>
                        <div class="info-value">{{ $depense->date_depense->format('d/m/Y') }}</div>
                    </div>
                    <div class="col-6 text-end">
                        <span class="section-label">Mode de règlement</span>
                        <div class="info-value">{{ ucfirst($depense->mode_paiement) }}</div>
                    </div>
                </div>

                <!-- Trainer & Formation Info -->
                <div class="trainer-box">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <span class="section-label">Bénéficiaire (Formateur)</span>
                            <div class="trainer-name">{{ $depense->trainer->name ?? $depense->beneficiaire }}</div>
                            <div class="small text-muted fw-bold">{{ $depense->formation->nom ?? 'N/A' }}</div>
                            @if($depense->groupeFormation)
                                <div class="small text-muted fw-bold">{{ $depense->groupeFormation->nom }}</div>
                            @endif
                            @if($depense->trainer && $depense->trainer->phone)
                                <div class="small text-muted" style="font-size: 0.75rem;"><i class="fas fa-phone me-1"></i> {{ $depense->trainer->phone }}</div>
                            @endif
                        </div>
                        <div class="col-4">
                            <div class="amount-card">
                                <div class="amount-val">{{ number_format($depense->montant, 0, ',', ' ') }}</div>
                                <div style="font-size: 8px; font-weight: 700; opacity: 0.8; letter-spacing: 1px;">FCFA</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Summary Details -->
                <div class="row">
                    <div class="col-12">
                        <table class="summary-table">
                            <tr>
                                <td class="text-muted">Type de Dépense</td>
                                <td class="text-end fw-bold">{{ $depense->categorie }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Libellé</td>
                                <td class="text-end fw-bold">{{ $depense->titre }}</td>
                            </tr>
                            @if($depense->reference)
                            <tr>
                                <td class="text-muted">Référence transaction</td>
                                <td class="text-end fw-bold">{{ $depense->reference }}</td>
                            </tr>
                            @endif
                            @if(!is_null($depense->montant_commission_initial))
                            <tr>
                                <td class="text-muted">Commission calculée</td>
                                <td class="text-end fw-bold">{{ number_format((float) $depense->montant_commission_initial, 0, ',', ' ') }} FCFA</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Volume horaire validé</td>
                                <td class="text-end fw-bold">{{ number_format((float) $depense->heures_validees, 2, ',', ' ') }} / {{ number_format((float) $depense->heures_prevues, 2, ',', ' ') }} h</td>
                            </tr>
                            @endif
                            @if((float) $depense->montant_retranchement > 0)
                            <tr>
                                <td class="text-muted">Retranchement</td>
                                <td class="text-end fw-bold text-danger">- {{ number_format((float) $depense->montant_retranchement, 0, ',', ' ') }} FCFA</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Motif</td>
                                <td class="text-end fw-bold">{{ $depense->motif_retranchement }}</td>
                            </tr>
                            @endif
                            <tr>
                                <td class="summary-total">Montant total réglé</td>
                                <td class="text-end summary-total text-success">
                                    {{ number_format($depense->montant, 0, ',', ' ') }} FCFA
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Signatures -->
                <div class="signature-area row mt-4">
                    <div class="col-6">
                        <div class="signature-box">
                            <span class="section-label">Signature Formateur</span>
                            <div class="signature-line"></div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="signature-box">
                            <span class="section-label">Émis par (Administration)</span>
                            <div class="fw-bold small text-warning">{{ $depense->creator->name ?? 'Responsable' }}</div>
                            @if($depense->retranchementDefinedBy)
                                <div class="small text-muted">Décision: {{ $depense->retranchementDefinedBy->name }}</div>
                            @endif
                            <div class="signature-line"></div>
                        </div>
                    </div>
                </div>

                <div class="footer">
                    <strong>SigLAB Technologie SARL</strong> - Bamako Boulkansoumbougou près du marché, Imm. Thioma Guidado en face de PMU-Mali - Tél : +223 93 38 73 25
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
