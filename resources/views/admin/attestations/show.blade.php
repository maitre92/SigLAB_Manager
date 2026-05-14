@extends('layouts.admin')

@section('content')
<div class="mb-4 d-print-none">
    <a href="{{ route('admin.attestations.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i> Retour
    </a>
    <button onclick="window.print()" class="btn btn-primary shadow-sm ms-2">
        <i class="fas fa-print me-1"></i> Imprimer l'attestation
    </button>
</div>

<div class="certificate-container shadow-lg mx-auto" id="certificate">
    <div class="certificate-border">
        <div class="certificate-inner">
            <div class="certificate-header">
                <div class="logo-box mb-3">
                    <i class="fas fa-graduation-cap fa-3x" style="color: var(--primary-color);"></i>
                </div>
                <h1 class="certificate-title">ATTESTATION DE RÉUSSITE</h1>
                <p class="reference">Réf: {{ $attestation->reference }}</p>
            </div>

            <div class="certificate-body">
                <p class="intro">Cette attestation est fièrement décernée à :</p>
                <h2 class="recipient-name">{{ $attestation->apprenant->nom_complet }}</h2>
                <p class="text">né(e) le {{ $attestation->apprenant->date_naissance ? $attestation->apprenant->date_naissance->format('d/m/Y') : '...' }} à {{ $attestation->apprenant->lieu_naissance ?? '...' }}</p>
                
                <p class="achievement">Pour avoir suivi avec succès et complété la formation de :</p>
                <h3 class="course-name">{{ $attestation->formation->nom }}</h3>
                
                <p class="details">
                    Session du {{ $attestation->formation->date_debut ? $attestation->formation->date_debut->format('d/m/Y') : '...' }} 
                    au {{ $attestation->formation->date_fin ? $attestation->formation->date_fin->format('d/m/Y') : '...' }}<br>
                    Volume horaire : {{ $attestation->formation->duree_heures }} heures
                </p>
            </div>

            <div class="certificate-footer">
                <div class="signature-box">
                    <p class="location">Fait à Bamako, le {{ $attestation->date_emission->format('d/m/Y') }}</p>
                    <div class="signature-line mt-5"></div>
                    <p class="signatory">Le Directeur du Centre</p>
                </div>
                <div class="stamp-box">
                    <div class="stamp">SIG-LAB</div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .certificate-container {
        width: 100%;
        max-width: 800px;
        background-color: #fff;
        padding: 40px;
        font-family: 'Georgia', serif;
        position: relative;
        color: #333;
    }

    .certificate-border {
        border: 20px solid #f8f9fa;
        border-image: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%) 1;
        padding: 10px;
    }

    .certificate-inner {
        border: 2px solid #ddd;
        padding: 50px;
        text-align: center;
        background-image: radial-gradient(circle at center, rgba(200, 200, 255, 0.05) 0%, transparent 70%);
    }

    .certificate-title {
        font-size: 42px;
        font-weight: 900;
        letter-spacing: 2px;
        margin-bottom: 5px;
        color: #1a2a44;
    }

    .reference {
        font-size: 14px;
        color: #666;
        font-family: 'Courier New', monospace;
    }

    .intro {
        margin-top: 40px;
        font-size: 20px;
        font-style: italic;
    }

    .recipient-name {
        font-size: 38px;
        font-weight: bold;
        text-decoration: underline;
        margin: 20px 0;
        color: #000;
    }

    .achievement {
        margin-top: 30px;
        font-size: 18px;
    }

    .course-name {
        font-size: 28px;
        font-weight: bold;
        color: var(--primary-color);
        margin: 15px 0;
    }

    .details {
        margin-top: 20px;
        line-height: 1.6;
    }

    .certificate-footer {
        margin-top: 60px;
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
    }

    .signature-box {
        text-align: left;
    }

    .signature-line {
        width: 200px;
        border-bottom: 2px solid #333;
    }

    .signatory {
        margin-top: 10px;
        font-weight: bold;
    }

    .stamp-box {
        opacity: 0.1;
    }

    .stamp {
        width: 100px;
        height: 100px;
        border: 4px double var(--primary-color);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 900;
        color: var(--primary-color);
        transform: rotate(-15deg);
        font-size: 18px;
    }

    @media print {
        body * {
            visibility: hidden;
        }
        #certificate, #certificate * {
            visibility: visible;
        }
        #certificate {
            position: absolute;
            left: 0;
            top: 0;
            width: 100% !important;
            max-width: none !important;
            box-shadow: none !important;
            padding: 0 !important;
        }
        .main-content {
            margin: 0 !important;
            padding: 0 !important;
        }
    }
</style>
@endsection
