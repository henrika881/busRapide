<?php

namespace Database\Factories;

use App\Models\Ticket;
use App\Models\Client;
use App\Models\Voyage;
use App\Models\Siege;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketFactory extends Factory
{
    protected $model = Ticket::class;

    public function definition()
    {
        // Récupère ou crée des données nécessaires
        $client = Client::inRandomOrder()->first() ?? Client::factory()->create();
        $voyage = Voyage::inRandomOrder()->first() ?? Voyage::factory()->create();
        
        // Récupère un siège disponible pour ce voyage
        $siege = Siege::where('idBus', $voyage->idBus)
            ->where('statut', 'libre')
            ->inRandomOrder()
            ->first() ?? Siege::factory()->create(['idBus' => $voyage->idBus]);
        
        $classes = ['standard', 'vip'];
        $classe = $this->faker->randomElement($classes);
        
        $prixBase = $classe === 'vip' ? $voyage->prixVIP : $voyage->prixStandard;
        $prixTotal = $prixBase + ($classe === 'vip' ? rand(5000, 15000) : 0);
        
        $statuts = ['en_attente', 'reserve', 'confirme', 'annule', 'utilise'];
        $statut = $this->faker->randomElement($statuts);
        
        return [
            'numeroBillet' => 'TICK-' . strtoupper($this->faker->bothify('???')) . '-' . date('Ymd') . '-' . $this->faker->numerify('####'),
            'idVoyage' => $voyage->idVoyage,
            'idClient' => $client->idClient,
            'idSiege' => $siege->idSiege,
            'prixPaye' => $prixTotal,
            'classeBillet' => $classe,
            'prixBase' => $prixBase,
            'statut' => $statut,
            'modePaiement' => $this->faker->randomElement(['carte', 'especes', 'mobile', 'virement']),
            'dateAchat' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'dateExpiration' => $statut === 'reserve' ? $this->faker->dateTimeBetween('+1 hour', '+48 hours') : null,
            'dateUtilisation' => $statut === 'utilise' ? $this->faker->dateTimeBetween('-5 days', 'now') : null,
            'codeQR' => 'QR-' . uniqid(),
            'idAgent' => 1, // ID de l'agent admin
            'notes' => $this->faker->optional()->sentence(),
            'created_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
        ];
    }
}