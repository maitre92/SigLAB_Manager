<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emploi du temps - {{ $groupe->nom }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 14mm;
        }

        :root {
            --primary: #243b6b;
            --border: #d8dee9;
            --muted: #667085;
            --light: #f4f7fb;
            --text: #111827;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            color: var(--text);
            font-family: Arial, Helvetica, sans-serif;
            background: #eef2f7;
            font-size: 13px;
        }

        .toolbar {
            position: sticky;
            top: 0;
            padding: 12px;
            background: #ffffff;
            border-bottom: 1px solid var(--border);
            text-align: right;
        }

        .toolbar button,
        .toolbar a {
            display: inline-block;
            border: 1px solid var(--primary);
            border-radius: 6px;
            padding: 8px 12px;
            background: var(--primary);
            color: #ffffff;
            text-decoration: none;
            font-weight: 700;
            cursor: pointer;
        }

        .toolbar a {
            background: #ffffff;
            color: var(--primary);
            margin-left: 8px;
        }

        .page {
            width: 210mm;
            min-height: 297mm;
            margin: 16px auto;
            padding: 16mm;
            background: #ffffff;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.12);
        }

        .header {
            border-bottom: 3px solid var(--primary);
            padding-bottom: 14px;
            margin-bottom: 18px;
        }

        .brand {
            font-size: 22px;
            font-weight: 800;
            color: var(--primary);
            margin-bottom: 6px;
        }

        .title {
            font-size: 24px;
            font-weight: 800;
            text-transform: uppercase;
            margin: 12px 0 4px;
            color: var(--text);
        }

        .subtitle {
            color: var(--muted);
            font-size: 13px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin: 18px 0;
        }

        .info-box {
            border: 1px solid var(--border);
            background: var(--light);
            border-radius: 6px;
            padding: 10px;
        }

        .label {
            display: block;
            color: var(--muted);
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .value {
            font-weight: 700;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }

        th {
            background: var(--primary);
            color: #ffffff;
            text-align: left;
            padding: 10px;
            font-size: 12px;
        }

        td {
            border: 1px solid var(--border);
            padding: 10px;
            vertical-align: top;
        }

        tr:nth-child(even) td {
            background: #f9fafb;
        }

        .plain {
            white-space: pre-wrap;
            border: 1px solid var(--border);
            background: #f9fafb;
            padding: 14px;
            border-radius: 6px;
            line-height: 1.6;
        }

        .empty {
            text-align: center;
            color: var(--muted);
            border: 1px dashed var(--border);
            padding: 30px;
            border-radius: 6px;
        }

        .footer {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
            color: var(--muted);
            font-size: 11px;
            border-top: 1px solid var(--border);
            padding-top: 10px;
        }

        @media print {
            body {
                background: #ffffff;
            }

            .toolbar {
                display: none;
            }

            .page {
                width: auto;
                min-height: auto;
                margin: 0;
                padding: 0;
                box-shadow: none;
            }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <button type="button" onclick="window.print()">Imprimer / Enregistrer en PDF</button>
        <a href="{{ route('admin.formations.show', $groupe->formation) }}">Retour</a>
    </div>

    <main class="page">
        <header class="header">
            <div class="brand">sigLAB Manager</div>
            <div class="title">Emploi du temps</div>
            <div class="subtitle">{{ $groupe->formation->nom }} - {{ $groupe->nom }}</div>
        </header>

        <section class="info-grid">
            <div class="info-box">
                <span class="label">Formation</span>
                <span class="value">{{ $groupe->formation->nom }}</span>
            </div>
            <div class="info-box">
                <span class="label">Groupe</span>
                <span class="value">{{ $groupe->nom }} ({{ $groupe->code }})</span>
            </div>
            <div class="info-box">
                <span class="label">Formateur principal</span>
                <span class="value">{{ $groupe->formateurPrincipal->name ?? 'Non défini' }}</span>
            </div>
            <div class="info-box">
                <span class="label">Période</span>
                <span class="value">
                    {{ $groupe->date_debut ? $groupe->date_debut->format('d/m/Y') : '?' }}
                    -
                    {{ $groupe->date_fin ? $groupe->date_fin->format('d/m/Y') : '?' }}
                </span>
            </div>
            <div class="info-box">
                <span class="label">Salle</span>
                <span class="value">{{ $groupe->salle ?: 'Non définie' }}</span>
            </div>
            <div class="info-box">
                <span class="label">Statut</span>
                <span class="value">{{ $groupe->statut_label }}</span>
            </div>
        </section>

        @if(!empty($schedule))
            <table>
                <thead>
                    <tr>
                        <th style="width: 24%;">Jour</th>
                        <th style="width: 24%;">Horaire</th>
                        <th>Activité / Module</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($schedule as $item)
                        <tr>
                            <td><strong>{{ $item['day'] ?: '---' }}</strong></td>
                            <td>{{ $item['start'] ?: '--:--' }} - {{ $item['end'] ?: '--:--' }}</td>
                            <td>{{ $item['activity'] ?: '---' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @elseif($plainSchedule)
            <div class="plain">{{ $plainSchedule }}</div>
        @else
            <div class="empty">Aucun emploi du temps n'a été renseigné pour ce groupe.</div>
        @endif

        <footer class="footer">
            <span>Document généré le {{ now()->format('d/m/Y à H:i') }}</span>
            <span>{{ $groupe->formation->code }} / {{ $groupe->code }}</span>
        </footer>
    </main>
</body>
</html>
