<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Export Tickets - BusRapide</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #3b82f6;
            padding-bottom: 20px;
        }
        .header h1 {
            color: #3b82f6;
            margin: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th {
            background-color: #3b82f6;
            color: white;
            padding: 8px;
            text-align: left;
        }
        td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            color: #666;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>BusRapide - Export des Tickets</h1>
        <p>Généré le: {{ date('d/m/Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Numéro</th>
                <th>Client</th>
                <th>Voyage</th>
                <th>Date</th>
                <th>Siège</th>
                <th>Prix</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tickets as $ticket)
            <tr>
                <td>{{ $ticket->numeroBillet }}</td>
                <td>{{ $ticket->client ? $ticket->client->nom . ' ' . $ticket->client->prenom : 'N/A' }}</td>
                <td>
                    @if($ticket->voyage && $ticket->voyage->trajet)
                        {{ $ticket->voyage->trajet->villeDepart }} → {{ $ticket->voyage->trajet->villeArrivee }}
                    @else
                        N/A
                    @endif
                </td>
                <td>{{ $ticket->voyage ? date('d/m/Y H:i', strtotime($ticket->voyage->dateHeureDepart)) : 'N/A' }}</td>
                <td>{{ $ticket->siege ? $ticket->siege->numeroSiege : 'N/A' }}</td>
                <td>{{ $ticket->prixPaye ? number_format($ticket->prixPaye, 0, ',', ' ') . ' FCFA' : 'N/A' }}</td>
                <td>
                    @switch($ticket->statut)
                        @case('en_attente') En attente @break
                        @case('reserve') Réservé @break
                        @case('confirme') Confirmé @break
                        @case('annule') Annulé @break
                        @case('utilise') Utilisé @break
                        @default {{ $ticket->statut }}
                    @endswitch
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>BusRapide - Tous droits réservés</p>
        <p>Page 1 sur 1</p>
    </div>
</body>
</html>