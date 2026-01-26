<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NelsiusPayService
{
    protected $baseUrl;
    protected $apiKey;

    public function __construct()
    {
        $this->baseUrl = 'https://api.nelsius.com/api/v1';
        $this->apiKey = env('NELSIUS_API_KEY');
    }

    /**
     * Initier un paiement mobile money
     */
    public function initiatePayment($amount, $phone, $operator, $description = 'Paiement Ticket')
    {
        $url = "{$this->baseUrl}/payments/mobile-money";

        try {
            Log::info("Nelsius Pay: Initiation paiement", [
                'amount' => $amount,
                'phone' => $phone,
                'operator' => $operator
            ]);

            $response = Http::withoutVerifying()->withHeaders([
                'X-API-KEY' => $this->apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post($url, [
                        'amount' => $amount,
                        'phone' => $phone,
                        'operator' => $operator, // 'orange_money' ou 'mtn_money'
                        'currency' => 'XAF',
                        'description' => $description
                    ]);

            Log::info("Nelsius Pay Response: " . $response->body());

            if ($response->successful()) {
                $data = $response->json();
                // Support both structures (direct or nested in transaction)
                $reference = $data['data']['transaction']['reference'] ?? $data['data']['reference'] ?? null;
                $status = $data['data']['transaction']['status'] ?? $data['data']['status'] ?? 'pending';

                return [
                    'success' => true,
                    'reference' => $reference,
                    'status' => $status,
                    'message' => $data['message'] ?? 'Paiement initié'
                ];
            }

            return [
                'success' => false,
                'message' => $response->json()['message'] ?? 'Erreur lors de l\'initiation du paiement',
                'details' => $response->json()
            ];

        } catch (\Exception $e) {
            Log::error("Nelsius Pay Error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erreur de connexion à la passerelle de paiement: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Vérifier le statut d'une transaction
     */
    public function checkTransactionStatus($reference)
    {
        $url = "{$this->baseUrl}/payments/mobile-money/{$reference}";

        try {
            $response = Http::withoutVerifying()->withHeaders([
                'X-API-KEY' => $this->apiKey,
                'Accept' => 'application/json',
            ])->get($url);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'status' => $data['data']['status'] ?? 'pending', // 'completed', 'failed', 'pending'
                    'data' => $data['data']
                ];
            }

            return [
                'success' => false,
                'message' => 'Impossible de vérifier le statut',
                'status' => 'unknown'
            ];

        } catch (\Exception $e) {
            Log::error("Nelsius Pay Check Error: " . $e->getMessage());
            // En cas d'erreur de connexion (timeout, ssl, etc), on dit que c'est "en attente"
            // pour que le polling continue et ne s'arrête pas brutelement.
            return [
                'success' => true, // On met true pour pas que le controller renvoie 400
                'status' => 'pending',
                'message' => 'Vérification en cours... (Connexion instable, tentative auto)'
            ];
        }
    }
}
