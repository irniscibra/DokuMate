<?php
return [
    'paths' => ['api/*'], // Erlaubt alle API-Routen
    'allowed_methods' => ['*'], // Erlaubt alle HTTP-Methoden (GET, POST, PUT, DELETE, etc.)
    'allowed_origins' => ['http://localhost:9000'], // Setze hier deine Frontend-Domain, z. B. ['https://deine-domain.com']
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'], // Erlaubt alle Header
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true, // Falls du mit Authentifizierung arbeitest
];
