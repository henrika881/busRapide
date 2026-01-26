<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Billet {{ $ticket->numeroBillet }} - BusRapide</title>
    <style>
        @page { margin: 0; }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #1e293b;
            margin: 0;
            padding: 40px;
            background-color: #f8fafc;
        }
        .ticket {
            background-color: #ffffff;
            border-radius: 20px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            max-width: 600px;
            margin: 0 auto;
        }
        .header {
            background-color: #0f172a;
            color: #ffffff;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #3b82f6;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .content {
            padding: 30px;
        }
        .section-title {
            font-size: 10px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .itinerary {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }
        .city {
            display: table-cell;
            vertical-align: middle;
        }
        .city.left { text-align: left; }
        .city.right { text-align: right; }
        .city p { margin: 0; font-size: 20px; font-weight: 800; }
        .bus-icon {
            display: table-cell;
            text-align: center;
            vertical-align: middle;
            color: #3b82f6;
            font-size: 24px;
        }
        .details-grid {
            display: table;
            width: 100%;
            border-top: 1px dashed #e2e8f0;
            padding-top: 20px;
        }
        .grid-item {
            display: table-cell;
            width: 33.33%;
            padding: 10px 0;
        }
        .grid-item p { margin: 0; font-size: 14px; font-weight: bold; }
        .qr-section {
            text-align: center;
            padding: 30px;
            border-top: 1px solid #e2e8f0;
            background-color: #fafafa;
        }
        .qr-code {
            width: 120px;
            height: 120px;
            margin: 0 auto 15px;
            background-color: #ffffff;
            padding: 5px;
            border: 1px solid #e2e8f0;
        }
        .ticket-footer {
            text-align: center;
            font-size: 10px;
            color: #64748b;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="ticket">
        <div class="header">
            <h1>BusRapide</h1>
            <p style="margin-top: 5px; opacity: 0.8; font-size: 12px;">Billet de Voyage Officiel</p>
        </div>

        <div class="content">
            <div style="display: table; width: 100%; margin-bottom: 20px;">
                <div style="display: table-cell;">
                    <span class="section-title">Numéro de Billet</span>
                    <p style="margin: 0; font-size: 16px; font-weight: bold; color: #3b82f6;">{{ $ticket->numeroBillet }}</p>
                </div>
                <div style="display: table-cell; text-align: right;">
                    <span class="section-title">Passager</span>
                    <p style="margin: 0; font-size: 14px; font-weight: bold;">{{ $ticket->client->nom }} {{ $ticket->client->prenom }}</p>
                </div>
            </div>

            <div class="itinerary">
                <div class="city left">
                    <span class="section-title">Départ</span>
                    <p>{{ $ticket->voyage->trajet->villeDepart }}</p>
                </div>
                <div class="bus-icon">🚌</div>
                <div class="city right">
                    <span class="section-title">Arrivée</span>
                    <p>{{ $ticket->voyage->trajet->villeArrivee }}</p>
                </div>
            </div>

            <div class="details-grid">
                <div class="grid-item">
                    <span class="section-title">Date</span>
                    <p>{{ date('d/m/Y', strtotime($ticket->voyage->dateHeureDepart)) }}</p>
                </div>
                <div class="grid-item">
                    <span class="section-title">Heure</span>
                    <p>{{ $ticket->voyage->heure_depart }}</p>
                </div>
                <div class="grid-item">
                    <span class="section-title">Siège</span>
                    <p>{{ $ticket->siege->numeroSiege }} ({{ strtoupper($ticket->classeBillet) }})</p>
                </div>
            </div>

            <div style="margin-top: 20px; padding-top: 20px; border-top: 1px dashed #e2e8f0;">
                <span class="section-title">Prix Payé</span>
                <p style="margin: 0; font-size: 20px; font-weight: 900; color: #1e293b;">{{ number_format($ticket->prixPaye, 0, ',', ' ') }} FCFA</p>
            </div>
        </div>

        <div class="qr-section">
            <div class="qr-code">
                  <img src="data:image/png;base64,{{ $ticket->codeQR }}" 
                       style="width: 100%; height: 100%;">
            </div>
            <p style="margin: 0; font-size: 12px; font-weight: bold; color: #64748b;">{{ $ticket->numeroBillet }}</p>
            <p style="margin-top: 5px; font-size: 10px; color: #94a3b8;">Présentez ce code QR lors de l'embarquement</p>
        </div>
    </div>

    <div class="ticket-footer">
        <p>Merci d'avoir choisi BusRapide. Bon voyage !</p>
        <p>© {{ date('Y') }} BusRapide - Tous droits réservés</p>
    </div>
</body>
</html>
