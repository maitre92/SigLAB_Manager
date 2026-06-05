<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérification attestation {{ $attestation->reference }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            background: #f3f5f8;
            color: #191062;
            font-family: Arial, Helvetica, sans-serif;
        }

        .verify-shell {
            max-width: 760px;
            margin: 0 auto;
            padding: 56px 18px;
        }

        .verify-card {
            background: #fff;
            border: 1px solid #dde2ea;
            border-top: 7px solid #ed0612;
            border-radius: 8px;
            box-shadow: 0 20px 55px rgba(25, 16, 98, .12);
            overflow: hidden;
        }

        .verify-header {
            padding: 28px 32px;
            border-bottom: 1px solid #edf0f5;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            border-radius: 999px;
            background: #e8f7ee;
            color: #14783c;
            font-weight: 800;
            font-size: 14px;
        }

        .verify-body {
            padding: 28px 32px 34px;
        }

        .info-row {
            display: grid;
            grid-template-columns: 180px 1fr;
            gap: 18px;
            padding: 14px 0;
            border-bottom: 1px solid #edf0f5;
        }

        .info-row:last-child {
            border-bottom: 0;
        }

        .info-label {
            color: #6f7480;
            font-weight: 700;
        }

        .info-value {
            color: #191062;
            font-weight: 800;
        }

        @media (max-width: 600px) {
            .info-row {
                grid-template-columns: 1fr;
                gap: 4px;
            }
        }
    </style>
</head>
<body>
    <main class="verify-shell">
        <section class="verify-card">
            <div class="verify-header">
                <div class="status-badge">✓ Attestation authentifiée</div>
                <h1 class="h3 fw-black mt-3 mb-1">SigLAB Technologie</h1>
                <p class="text-muted mb-0">Vérification officielle de l'attestation de formation.</p>
            </div>
            <div class="verify-body">
                <div class="info-row">
                    <div class="info-label">Numéro</div>
                    <div class="info-value">{{ $attestation->reference }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Bénéficiaire</div>
                    <div class="info-value">{{ $attestation->apprenant->nom_complet ?? 'Apprenant non défini' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Formation</div>
                    <div class="info-value">{{ $attestation->formation->nom ?? 'Formation non définie' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Groupe</div>
                    <div class="info-value">{{ $attestation->groupeFormation->nom ?? 'Groupe non défini' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Date d'émission</div>
                    <div class="info-value">{{ $attestation->date_emission?->format('d/m/Y') }}</div>
                </div>
            </div>
        </section>
    </main>
</body>
</html>
