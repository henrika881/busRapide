<?php

require 'vendor/autoload.php';

$baseUrl = 'http://127.0.0.1:8000/api';
$client = new \GuzzleHttp\Client(['base_uri' => $baseUrl, 'http_errors' => false]);

echo "=== Démarrage de la vérification de l'authentification standard ===\n\n";

// 1. Inscription
echo "1. Test Inscription (Register)...\n";
$email = 'test_' . time() . '@example.com';
$password = 'password123';
$userData = [
    'nom' => 'Test',
    'prenom' => 'User',
    'email' => $email,
    'password' => $password,
    'password_confirmation' => $password,
    'telephone' => '0123456789'
];

try {
    $response = $client->post('/auth/register', [
        'json' => $userData,
        'headers' => ['Accept' => 'application/json']
    ]);

    $statusCode = $response->getStatusCode();
    $body = json_decode($response->getBody(), true);

    if ($statusCode === 201 || $statusCode === 200) {
        echo "✅ Inscription réussie.\n";
    } else {
        echo "❌ Échec de l'inscription (Status: $statusCode).\n";
        print_r($body);
        exit(1); // Arrêter si l'inscription échoue
    }

} catch (\Exception $e) {
    echo "❌ Erreur critique lors de l'inscription : " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n";

// 2. Connexion
echo "2. Test Connexion (Login)...\n";
try {
    $response = $client->post('/auth/login', [
        'json' => [
            'email' => $email,
            'password' => $password
        ],
        'headers' => ['Accept' => 'application/json']
    ]);

    $statusCode = $response->getStatusCode();
    $body = json_decode($response->getBody(), true);

    if ($statusCode === 200 && isset($body['token'])) {
        $token = $body['token'];
        echo "✅ Connexion réussie. Token récupéré.\n";
    } else {
        echo "❌ Échec de la connexion (Status: $statusCode).\n";
        print_r($body);
        exit(1);
    }

} catch (\Exception $e) {
    echo "❌ Erreur critique lors de la connexion : " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n";

// 3. Profil (Route protégée)
echo "3. Test Accès Profil (Protected Route)...\n";
try {
    $response = $client->get('/profile', [
        'headers' => [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ]
    ]);

    $statusCode = $response->getStatusCode();
    $body = json_decode($response->getBody(), true);

    if ($statusCode === 200) {
        echo "✅ Accès profil réussi.\n";
        // print_r($body);
    } else {
        echo "❌ Échec accès profil (Status: $statusCode).\n";
        print_r($body);
    }

} catch (\Exception $e) {
    echo "❌ Erreur critique accès profil : " . $e->getMessage() . "\n";
}

echo "\n";

// 4. Logout
echo "4. Test Déconnexion (Logout)...\n";
try {
    $response = $client->post('/auth/logout', [
        'headers' => [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ]
    ]);

    $statusCode = $response->getStatusCode();
    
    if ($statusCode === 200) {
        echo "✅ Déconnexion réussie.\n";
    } else {
        echo "❌ Échec déconnexion (Status: $statusCode).\n";
        echo $response->getBody();
    }

} catch (\Exception $e) {
    echo "❌ Erreur critique déconnexion : " . $e->getMessage() . "\n";
}

echo "\n=== Fin de la vérification ===\n";
