<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>{{ $page_title ?? 'Attestation' }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 0;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            background: #fff;
            color: #191062;
            font-family: DejaVu Sans, Arial, sans-serif;
        }

        .page {
            position: relative;
            width: 297mm;
            height: 210mm;
            overflow: hidden;
            background: #fff;
            page-break-after: always;
        }

        .page:last-child {
            page-break-after: auto;
        }

        .frame {
            position: absolute;
            left: 3mm;
            top: 3mm;
            right: 3mm;
            bottom: 3mm;
            border: .35mm solid #b8bbc4;
        }

        .inner-frame {
            display: none;
        }

        .blue-right {
            display: none;
        }

        .red-top-left {
            position: absolute;
            left: 0;
            top: 0;
            width: 118mm;
            height: 15mm;
            background: #ed0612;
        }

        .red-left {
            position: absolute;
            left: 0;
            top: 0;
            width: 15mm;
            height: 105mm;
            background: #ed0612;
        }

        .top-left-cut {
            position: absolute;
            left: 70mm;
            top: 0;
            width: 54mm;
            height: 22mm;
            background: #fff;
            transform: skewX(-24deg);
        }

        .blue-corner {
            position: absolute;
            left: 13mm;
            top: 13mm;
            width: 0;
            height: 0;
            border-top: 43mm solid #191062;
            border-right: 43mm solid transparent;
        }

        .blue-top {
            position: absolute;
            left: 118mm;
            right: 0;
            top: 0;
            height: 5mm;
            background: #191062;
        }

        .right-blue-corner {
            position: absolute;
            right: 0;
            top: 0;
            width: 6mm;
            height: 210mm;
            background: #191062;
        }

        .top-right-ribbon {
            position: absolute;
            top: -2mm;
            right: -0.5mm;
            width: 52mm;
            height: 74mm;
            z-index: 4;
        }

        .blue-bottom {
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            height: 12mm;
            background: #191062;
        }

        .blue-bottom-corner {
            position: absolute;
            left: 0;
            bottom: 15mm;
            width: 0;
            height: 0;
            border-bottom: 20mm solid #191062;
            border-right: 20mm solid transparent;
        }

        .red-bottom {
            position: absolute;
            left: 0;
            bottom: 1mm;
            width: 132mm;
            height: 4mm;
            background: #ed0612;
        }

        .logo {
            position: absolute;
            top: 8mm;
            left: 100mm;
            width: 96mm;
            height: 31mm;
            object-fit: contain;
        }

        .seal {
            position: absolute;
            top: 34mm;
            right: 49mm;
            width: 27mm;
            height: 32mm;
        }

        .seal-gold {
            position: absolute;
            left: 3mm;
            top: 0;
            width: 22mm;
            height: 22mm;
            border-radius: 22mm;
            background: #d9b851;
        }

        .seal-blue {
            position: absolute;
            left: 7mm;
            top: 4mm;
            width: 14mm;
            height: 14mm;
            border-radius: 14mm;
            background: #191062;
            color: #fff;
            text-align: center;
            font-size: 9mm;
            line-height: 14mm;
            font-weight: 900;
        }

        .ribbon-left,
        .ribbon-right {
            position: absolute;
            top: 19mm;
            width: 6mm;
            height: 17mm;
            background: #191062;
        }

        .ribbon-left {
            left: 8mm;
            transform: rotate(10deg);
        }

        .ribbon-right {
            right: 8mm;
            transform: rotate(-10deg);
        }

        .title {
            position: absolute;
            top: 43mm;
            left: 45mm;
            right: 45mm;
            text-align: center;
        }

        .title h1 {
            margin: 0;
            color: #191062;
            font-size: 17mm;
            line-height: 1;
            letter-spacing: 3mm;
            font-weight: 900;
        }

        .title h2 {
            margin: 7mm 0 0;
            color: #ed0612;
            font-size: 8.5mm;
            line-height: 1;
            letter-spacing: 1.2mm;
            font-weight: 900;
        }

        .reference {
            display: none;
        }

        .intro {
            position: absolute;
            top: 83mm;
            left: 42mm;
            right: 42mm;
            color: #70727a;
            text-align: center;
            font-family: DejaVu Serif, Georgia, serif;
            font-size: 4.8mm;
            line-height: 1.42;
            font-weight: 700;
        }

        .student {
            position: absolute;
            top: 109mm;
            left: 16mm;
            right: 16mm;
            color: #102c66;
            text-align: center;
            font-family: DejaVu Serif, Georgia, serif;
            font-size: 14mm;
            line-height: 1;
            font-weight: 900;
        }

        .training {
            position: absolute;
            top: 127mm;
            left: 26mm;
            right: 26mm;
            color: #737373;
            text-align: center;
            font-size: 4.8mm;
            line-height: 1.5;
            font-weight: 500;
        }

        .training strong {
            font-weight: 900;
        }

        .legal {
            position: absolute;
            top: 143mm;
            left: 20mm;
            right: 20mm;
            color: #c5161d;
            text-align: center;
            font-size: 4.35mm;
            line-height: 1.2;
            font-weight: 900;
        }

        .place {
            position: absolute;
            left: 28mm;
            bottom: 35mm;
            width: 60mm;
            color: #102c66;
            text-align: center;
            font-size: 5.2mm;
            line-height: 2;
            font-weight: 900;
        }

        .ornament {
            position: absolute;
            left: 105mm;
            bottom: 39mm;
            width: 78mm;
            color: #d9b851;
            text-align: center;
            font-size: 18mm;
            line-height: 1;
        }

        .director {
            position: absolute;
            right: 26mm;
            bottom: 34mm;
            width: 82mm;
            color: #102c66;
            text-align: center;
            font-size: 5.1mm;
            font-weight: 900;
        }

        .director .line {
            margin: 3mm auto 2mm;
            width: 48mm;
            border-top: .55mm solid #102c66;
        }

        .director-name {
            white-space: nowrap;
            font-size: 4.8mm;
        }

        .footer {
            position: absolute;
            left: 8mm;
            bottom: 6.8mm;
            color: #fff;
            font-family: DejaVu Serif, Georgia, serif;
            font-size: 4.3mm;
            font-weight: 900;
        }

        .page-count {
            position: absolute;
            right: 21mm;
            bottom: 5mm;
            color: #bfc0c8;
            font-family: DejaVu Serif, Georgia, serif;
            font-size: 7mm;
        }
    </style>
</head>
<body>
@php
    $certificateList = isset($attestations) ? $attestations : collect([$attestation]);
    $months = [
        1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
        5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
        9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre',
    ];
    $formatLongDate = function ($date) use ($months) {
        if (!$date) {
            return '......';
        }

        return $date->format('d') . ' ' . $months[(int) $date->format('m')] . ' ' . $date->format('Y');
    };
@endphp
@foreach($certificateList as $attestation)
@php
    $apprenant = $attestation->apprenant;
    $formation = $attestation->formation;
    $groupe = $attestation->groupeFormation;
    $dateDebut = $formatLongDate($groupe?->date_debut);
    $dateFin = $formatLongDate($groupe?->date_fin);
@endphp
    <section class="page">
        <div class="frame"></div>
        <div class="inner-frame"></div>
        <div class="blue-right"></div>
        <div class="red-top-left"></div>
        <div class="red-left"></div>
        <div class="top-left-cut"></div>
        <div class="blue-corner"></div>
        <div class="blue-top"></div>
        <div class="right-blue-corner"></div>
        <img src="{{ public_path('images/attestation_right_ribbon.png') }}" alt="" class="top-right-ribbon">
        <div class="blue-bottom"></div>
        <div class="blue-bottom-corner"></div>
        <div class="red-bottom"></div>

        <img class="logo" src="{{ public_path('images/logo_siglab.jpeg') }}" alt="SigLAB Technologie">

        <div class="seal">
            <div class="ribbon-left"></div>
            <div class="ribbon-right"></div>
            <div class="seal-gold"></div>
            <div class="seal-blue">V</div>
        </div>

        <div class="title">
            <h1>ATTESTATION</h1>
            <h2>DE FORMATION</h2>
            <div class="reference">N° d'identification : {{ $attestation->reference }}</div>
        </div>

        <p class="intro">
            Je soussigné, monsieur Abdoulaye Mahamane, directeur général du<br>
            centre de formation sigLAB technologie atteste que :
        </p>

        <div class="student">{{ $apprenant->nom_complet ?? 'Apprenant non défini' }}</div>

        <p class="training">
            A suivi (e) avec succès et assiduité une formation pratique sur le module
            <strong>{{ $formation->nom ?? 'Formation non définie' }}</strong>
            à la date du <strong>{{ $dateDebut }}</strong> au <strong>{{ $dateFin }}</strong>
        </p>

        <p class="legal">
            En foi de quoi la présente attestation lui est délivrée pour servir ce que de droit
        </p>

        <div class="place">
            <div>BAMAKO,</div>
            <div>LE&nbsp;&nbsp;{{ $groupe?->date_fin ? $groupe->date_fin->format('d/m/Y') : '......../......../20....' }}</div>
        </div>

        <div class="ornament">❧ ✦ ❧</div>

        <div class="director">
            <div>DIRECTEUR GÉNÉRAL</div>
            <div class="line"></div>
            <div class="director-name">ABDOULAYE MAHAMANE</div>
        </div>

        <div class="footer">La présente attestation n'est délivrée qu'une fois</div>
        <div class="page-count">1/1</div>
    </section>
@endforeach
</body>
</html>
