<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NelsiusPayService
{

    protected $baseUrl;
    protected $apiKey;
    protected $secretkey;

    public function __construct()
    {
        $this->baseUrl = 'https://api.nelsius.com/api/v1';
        $this->apiKey = env('NELSIUS_API_KEY');
        $this->secretkey = env('NELSIUS_API_SECRET');
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
                'X-API-SECRET' => $this->secretkey,
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

                // Log complete data structure to confirm nesting
                Log::debug("Nelsius Pay JSON Decoded", ['data' => $data]);

                //Extraction de la référence propre (UUID d'id de référence)
                $reference = $data['data']['transaction']['reference_id'] ??
                    $data['data']['gateway_reference'] ??
                    $data['data']['reference'] ??
                    null;

                // Extraction du statut initial
                $status = $data['data']['status'] ?? 'pending';

                return [
                    'success' => true,
                    'reference' => $reference,
                    'status' => strtolower($status),
                    'message' => $data['message'] ?? $data['data']['message'] ?? 'Paiement initié'
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
                'X-API-SECRET' => $this->secretkey,
                'Accept' => 'application/json',
            ])->get($url);

            if ($response->successful()) {
                $data = $response->json();
                $payload = $data['data'] ?? [];

                // On détermine le statut normalisé
                $status = 'pending';
                if (($payload['is_completed'] ?? false) || ($payload['status'] ?? '') === 'SUCCESSFUL' || ($payload['status'] ?? '') === 'COMPLETED') {
                    $status = 'completed';
                } elseif (($payload['is_failed'] ?? false) || ($payload['status'] ?? '') === 'FAILED') {
                    $status = 'failed';
                }

                return [
                    'success' => true,
                    'status' => $status,
                    'data' => $payload
                ];
            }

            $errorMsg = $response->json()['message'] ?? 'Impossible de vérifier le statut sur la passerelle';
            Log::warning("Nelsius Pay Status Check Failed: " . $response->body());

            return [
                'success' => false,
                'message' => $errorMsg,
                'status' => 'unknown',
                'details' => $response->json()
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
