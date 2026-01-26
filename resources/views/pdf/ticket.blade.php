<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Billet BusRapide - {{ $ticket->numeroBillet }}</title>
    <style>
        body { font-family: sans-serif; }
        .ticket-box { border: 2px solid #333; padding: 20px; border-radius: 10px; position: relative; }
        .header { border-bottom: 2px solid #eee; padding-bottom: 10px; margin-bottom: 20px; display: flex; justify-content: space-between; }
        .logo { font-size: 24px; font-weight: bold; color: #2563eb; }
        .info-grid { display: table; width: 100%; margin-bottom: 20px; }
        .info-row { display: table-row; }
        .info-cell { display: table-cell; padding: 5px; width: 50%; }
        .label { font-weight: bold; color: #666; font-size: 12px; text-transform: uppercase; }
        .value { font-size: 16px; font-weight: bold; }
        .qr-code { text-align: center; margin-top: 30px; }
        .footer { text-align: center; font-size: 10px; color: #999; margin-top: 20px; border-top: 1px solid #eee; padding-top: 10px; }
        .vip-badge { position: absolute; top: 20px; right: 20px; background: #9333ea; color: white; padding: 5px 15px; border-radius: 20px; font-weight: bold; font-size: 12px; }
    </style>
</head>
<body>
    <div class="ticket-box">
        @if($ticket->classeBillet === 'vip')
            <div class="vip-badge">TICKET VIP</div>
        @endif

        <div class="header">
            <div class="logo">BusRapide CA</div>
        </div>

        <div class="info-grid">
            <div class="info-row">
                <div class="info-cell">
                    <div class="label">Passager</div>
                    <div class="value">{{ $ticket->client->prenom }} {{ $ticket->client->nom }}</div>
                </div>
                <div class="info-cell">
                    <div class="label">Billet N°</div>
                    <div class="value">{{ $ticket->numeroBillet }}</div>
                </div>
            </div>
            <div class="info-row">
                <div class="info-cell">
                    <div class="label">Départ</div>
                    <div class="value">{{ $ticket->voyage->trajet->villeDepart }}</div>
                    <div style="font-size: 12px; color: #666;">{{ $ticket->voyage->dateHeureDepart->format('H:i') }}</div>
                </div>
                <div class="info-cell">
                    <div class="label">Arrivée</div>
                    <div class="value">{{ $ticket->voyage->trajet->villeArrivee }}</div>
                    <div style="font-size: 12px; color: #666;">~{{ $ticket->voyage->dateHeureArrivee ? $ticket->voyage->dateHeureArrivee->format('H:i') : '--:--' }}</div>
                </div>
            </div>
            <div class="info-row">
                 <div class="info-cell">
                    <div class="label">Date de voyage</div>
                    <div class="value">{{ $ticket->voyage->dateHeureDepart->format('d/m/Y') }}</div>
                </div>
                <div class="info-cell">
                    <div class="label">Siège</div>
                    <div class="value">N°{{ $ticket->siege->numeroSiege }}</div>
                </div>
            </div>
        </div>

        <div class="qr-code">
            <!-- QR Code generated as image -->
            <img src="data:image/png;base64, {{ $ticket->codeQR ?: base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')->size(150)->generate($ticket->numeroBillet)) }}" width="150">
            <p style="font-size: 10px; margin-top: 5px;">Scannez ce code à l'embarquement</p>
        </div>

        <div class="footer">
            Ce billet est personnel et non cessible. Présentez-vous 30min avant le départ.<br>
            BusRapide - Voyagez en toute confiance.
        </div>
    </div>
</body>
</html>
