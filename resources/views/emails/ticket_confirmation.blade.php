<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Arial', sans-serif; color: #333; line-height: 1.6; }
        .container { width: 100%; max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #2563eb; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background-color: #f9fafb; padding: 20px; border: 1px solid #e5e7eb; border-top: none; border-radius: 0 0 8px 8px; }
        .btn { display: inline-block; background-color: #2563eb; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-top: 20px; font-weight: bold; }
        .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #6b7280; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Confirmation de Réservation</h1>
        </div>
        <div class="content">
            <p>Bonjour {{ $ticket->client->prenom }},</p>
            <p>Votre réservation est confirmée ! Vous trouverez ci-joint votre billet électronique au format PDF.</p>
            
            <div style="background: white; padding: 15px; border-radius: 8px; margin: 20px 0; border: 1px solid #e5e7eb;">
                <p><strong>Voyage :</strong> {{ $ticket->voyage->trajet->villeDepart }} ➝ {{ $ticket->voyage->trajet->villeArrivee }}</p>
                <p><strong>Date :</strong> {{ $ticket->voyage->dateHeureDepart->translatedFormat('l d F Y à H:i') }}</p>
                <p><strong>Siège :</strong> N°{{ $ticket->siege->numeroSiege }} ({{ strtoupper($ticket->classeBillet) }})</p>
                <p><strong>Prix :</strong> {{ number_format($ticket->prixPaye, 0, ',', ' ') }} FCFA</p>
            </div>

            <p>Merci de présenter le QR Code présent sur le billet lors de votre embarquement.</p>
            
            <center>
                <a href="{{ url('/profil') }}" class="btn">Voir ma réservation</a>
            </center>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} BusRapide. Tous droits réservés.
        </div>
    </div>
</body>
</html>