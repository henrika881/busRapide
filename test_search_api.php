<?php
/**
 * Script de test pour l'API de recherche de voyages
 */

$url = 'http://127.0.0.1:8000/api/voyages/search?ville_depart=douala&ville_arrivee=yaounde&date_voyage=2026-01-15';

echo "🔍 Test de l'API de recherche de voyages\n";
echo "URL: $url\n";
echo str_repeat("=", 60) . "\n\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'Content-Type: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    echo "❌ Erreur cURL: $error\n";
    exit(1);
}

echo "📊 Code HTTP: $httpCode\n";
echo str_repeat("-", 60) . "\n";

if ($httpCode === 200) {
    $data = json_decode($response, true);
    
    if (isset($data['success']) && $data['success']) {
        echo "✅ Recherche réussie!\n\n";
        
        $exactCount = isset($data['data']) ? count($data['data']) : 0;
        $similarCount = isset($data['similar']) ? count($data['similar']) : 0;
        
        echo "📍 Résultats exacts: $exactCount voyage(s)\n";
        echo "📍 Résultats similaires: $similarCount voyage(s)\n\n";
        
        if ($exactCount > 0) {
            echo "Détails des voyages exacts:\n";
            foreach ($data['data'] as $index => $voyage) {
                echo "  " . ($index + 1) . ". " . ($voyage['trajet']['villeDepart'] ?? 'N/A') . 
                     " → " . ($voyage['trajet']['villeArrivee'] ?? 'N/A') . 
                     " | Départ: " . ($voyage['heure_depart'] ?? 'N/A') . 
                     " | Prix: " . ($voyage['prix'] ?? 'N/A') . " FCFA" .
                     " | Places: " . ($voyage['places_disponibles'] ?? 'N/A') . "\n";
            }
        } else {
            echo "ℹ️  Aucun voyage exact trouvé pour cette recherche.\n";
        }
        
        if ($similarCount > 0) {
            echo "\nVoyages similaires (dates alternatives):\n";
            foreach ($data['similar'] as $index => $voyage) {
                echo "  " . ($index + 1) . ". " . ($voyage['trajet']['villeDepart'] ?? 'N/A') . 
                     " → " . ($voyage['trajet']['villeArrivee'] ?? 'N/A') . 
                     " | Date: " . ($voyage['date_depart'] ?? 'N/A') .
                     " | Départ: " . ($voyage['heure_depart'] ?? 'N/A') . 
                     " | Prix: " . ($voyage['prix'] ?? 'N/A') . " FCFA\n";
            }
        }
        
    } else {
        echo "⚠️  La recherche a échoué.\n";
        echo "Message: " . ($data['message'] ?? 'Aucun message d\'erreur') . "\n";
    }
    
    echo "\n" . str_repeat("=", 60) . "\n";
    echo "📄 Réponse JSON complète:\n";
    echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
    
} else {
    echo "❌ Erreur HTTP $httpCode\n";
    echo "Réponse: $response\n";
}
