@extends('layouts.admin')

@section('content')

<!-- Boutons d'action cachés à l'impression -->
<div class="mb-4 d-print-none text-center text-md-start">
    <a href="{{ route('admin.attestations.index') }}" class="btn btn-dark rounded-pill px-4 shadow-sm">
        <i class="fas fa-arrow-left me-2"></i> Retour
    </a>
    <button onclick="window.print()" class="btn btn-primary rounded-pill px-4 ms-2 shadow-sm">
        <i class="fas fa-print me-2"></i> Imprimer l'attestation
    </button>
</div>

<!-- Zone du Certificat -->
<div class="certificate-container">
    <div class="certificate" id="certificate">

        <!-- Coins décoratifs -->
        <div class="corner tl"></div>
        <div class="corner tr"></div>
        <div class="corner bl"></div>
        <div class="corner br"></div>

        <!-- Bordures intérieures -->
        <div class="inner-border"></div>
        <div class="thin-border"></div>

        <!-- Filigrane d'arrière-plan -->
        <div class="watermark">SIG-LAB</div>

        <div class="content">
            <!-- En-tête -->
            <div class="header">
                <div class="logo-container">
                    <div class="logo-icon">
                        <i class="fas fa-award"></i>
                    </div>
                </div>
                <h1>ATTESTATION DE RÉUSSITE</h1>
                <div class="decorative-separator">
                    <span class="line"></span>
                    <span class="diamond"></span>
                    <span class="line"></span>
                </div>
                <p class="ref">Réf : <span>{{ $attestation->reference }}</span></p>
            </div>

            <!-- Corps du texte -->
            <div class="body">
                <p class="certify-text">Le centre de formation certifie que :</p>
                
                <h2 class="student-name">{{ $attestation->apprenant->nom_complet }}</h2>
                
                <p class="birth-details">
                    Né(e) le <span class="highlight">{{ $attestation->apprenant->date_naissance ? $attestation->apprenant->date_naissance->format('d/m/Y') : '...' }}</span> 
                    à <span class="highlight">{{ $attestation->apprenant->lieu_naissance ?? '...' }}</span>
                </p>

                <p class="success-text">a suivi et validé avec succès la formation :</p>
                
                <h3 class="course-name">{{ $attestation->formation->nom }}</h3>

                <div class="course-details">
                    <p>Période : du <strong>{{ $attestation->formation->date_debut ? $attestation->formation->date_debut->format('d/m/Y') : '...' }}</strong> au <strong>{{ $attestation->formation->date_fin ? $attestation->formation->date_fin->format('d/m/Y') : '...' }}</strong></p>
                    <p>Volume horaire : <strong>{{ $attestation->formation->duree_heures }} heures</strong></p>
                </div>
            </div>

            <!-- Pied de page (Signatures / Cachet) -->
            <div class="footer">
                <div class="date-location">
                    Fait à Bamako, le {{ $attestation->date_emission->format('d/m/Y') }}
                </div>
                
                <div class="footer-sign-block">
                    <div class="signature-section">
                        <p class="signature-title">Le Directeur Général</p>
                        <div class="signature-line"></div>
                        <p class="signature-sub">(Signature et mentions)</p>
                    </div>

                    <div class="stamp-box">
                        <div class="stamp">
                            <span>SIG-LAB</span>
                            <small>OFFICIEL</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Importation de polices élégantes */
@import url('https://fonts.googleapis.com/css2?family=Cinzel:wght@500;700&family=Montserrat:wght@300;400;600;700&display=swap');

/* Variables de couleurs */
:root {
    --primary-color: #0d2c54;   /* Bleu nuit haut de gamme */
    --accent-color: #c5a880;    /* Or / Bronze élégant */
    --text-dark: #222222;
    --text-muted: #666666;
}

body {
    background: #f4f6f9;
}

.certificate-container {
    padding: 20px 0;
    overflow-x: auto;
}

/* ===== STRUCTURE DU CERTIFICAT ===== */
.certificate {
    width: 1120px;
    height: 792px; /* Format A4 Paysage proportionnel */
    margin: 0 auto;
    background: #ffffff;
    padding: 60px 80px;
    position: relative;
    box-shadow: 0 15px 35px rgba(0,0,0,0.08);
    box-sizing: border-box;
    font-family: 'Montserrat', sans-serif;
    color: var(--text-dark);
}

/* Bordures & Coins */
.inner-border {
    position: absolute;
    inset: 25px;
    border: 2px solid var(--primary-color);
    pointer-events: none;
}

.thin-border {
    position: absolute;
    inset: 32px;
    border: 1px solid var(--accent-color);
    pointer-events: none;
}

.corner {
    width: 45px;
    height: 45px;
    position: absolute;
    border: 4px solid var(--accent-color);
    z-index: 5;
}
.tl { top: 15px; left: 15px; border-right: none; border-bottom: none; }
.tr { top: 15px; right: 15px; border-left: none; border-bottom: none; }
.bl { bottom: 15px; left: 15px; border-right: none; border-top: none; }
.br { bottom: 15px; right: 15px; border-left: none; border-top: none; }

/* Filigrane */
.watermark {
    position: absolute;
    top: 53%;
    left: 50%;
    transform: translate(-50%, -50%) rotate(-25deg);
    font-size: 130px;
    font-weight: 900;
    letter-spacing: 10px;
    color: rgba(13, 44, 84, 0.03);
    user-select: none;
    pointer-events: none;
    font-family: 'Cinzel', serif;
}

.content {
    position: relative;
    z-index: 2;
    text-align: center;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

/* ===== EN-TÊTE ===== */
.logo-container {
    margin-bottom: 10px;
}
.logo-icon {
    width: 65px;
    height: 65px;
    margin: 0 auto;
    background: linear-gradient(135deg, var(--primary-color), #1a447a);
    color: var(--accent-color);
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 28px;
    box-shadow: 0 4px 10px rgba(13, 44, 84, 0.2);
}

.header h1 {
    font-family: 'Cinzel', serif;
    font-weight: 700;
    font-size: 34px;
    color: var(--primary-color);
    margin: 15px 0 5px 0;
    letter-spacing: 2px;
}

.decorative-separator {
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 10px 0;
}
.decorative-separator .line {
    width: 100px;
    height: 1px;
    background-color: var(--accent-color);
}
.decorative-separator .diamond {
    width: 8px;
    height: 8px;
    background-color: var(--primary-color);
    transform: rotate(45deg);
    margin: 0 10px;
}

.ref {
    font-size: 12px;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 1px;
}
.ref span { font-weight: 600; color: var(--text-dark); }

/* ===== CORPS DU TEXTE ===== */
.body {
    margin: 20px 0;
}
.certify-text, .success-text {
    font-size: 16px;
    color: var(--text-muted);
    font-style: italic;
    margin-bottom: 10px;
}

.student-name {
    font-family: 'Cinzel', serif;
    font-size: 38px;
    font-weight: 700;
    color: var(--primary-color);
    margin: 15px 0;
    letter-spacing: 1px;
}

.birth-details {
    font-size: 15px;
    margin-bottom: 25px;
}
.highlight {
    font-weight: 600;
    color: var(--primary-color);
}

.course-name {
    font-size: 24px;
    font-weight: 700;
    color: var(--accent-color);
    margin: 15px 0;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.course-details {
    font-size: 14px;
    color: #444;
    line-height: 1.6;
}

/* ===== PIED DE PAGE ===== */
.footer {
    margin-top: 20px;
}
.date-location {
    text-align: right;
    font-size: 14px;
    font-style: italic;
    margin-bottom: 15px;
    padding-right: 20px;
}
.footer-sign-block {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    padding: 0 20px;
}

.signature-section {
    text-align: left;
    width: 250px;
}
.signature-title {
    font-weight: 700;
    font-size: 14px;
    color: var(--primary-color);
    margin-bottom: 0;
}
.signature-line {
    width: 100%;
    border-bottom: 1px dashed var(--primary-color);
    margin: 45px 0 5px 0;
}
.signature-sub {
    font-size: 11px;
    color: var(--text-muted);
    font-style: italic;
}

/* Cachet Réinventé */
.stamp-box {
    perspective: 100px;
}
.stamp {
    width: 100px;
    height: 100px;
    border: 3px double rgba(197, 168, 128, 0.8);
    border-radius: 50%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    transform: rotate(-12deg);
    color: var(--accent-color);
    font-family: 'Cinzel', serif;
    background: rgba(255, 255, 255, 0.8);
    box-shadow: 0 0 8px rgba(197, 168, 128, 0.1);
}
.stamp span {
    font-weight: 700;
    font-size: 14px;
    letter-spacing: 1px;
    line-height: 1;
}
.stamp small {
    font-size: 9px;
    letter-spacing: 2px;
    margin-top: 4px;
    font-family: 'Montserrat', sans-serif;
    font-weight: bold;
}

/* ===== CONFIGURATION IMPRESSION ===== */
@page {
    size: landscape;
    margin: 0; /* Évite les headers/footers du navigateur */
}

@media print {
    /* Masquer tout sauf le certificat */
    body * {
        visibility: hidden;
    }
    .certificate-container, #certificate, #certificate * {
        visibility: visible;
    }
    .certificate-container {
        padding: 0;
        margin: 0;
    }
    #certificate {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        box-shadow: none;
        padding: 60px 80px;
        background: #ffffff !important;
        -webkit-print-color-adjust: exact; /* Force le rendu des couleurs */
        print-color-adjust: exact;
    }
    .d-print-none {
        display: none !important;
    }
}
</style>

@endsection