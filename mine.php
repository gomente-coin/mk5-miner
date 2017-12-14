<?php

require __DIR__.'/vendor/autoload.php';

use GuzzleHttp\Client;

$baseUri       = 'https://mine-battlers.herokuapp.com';
$challengePath = '/api/pow/challenge';
$responsePath  = '/api/pow/response';
$apiToken      = 'Paste your API token embedded in the mining page.';

$client = new Client([
    'base_uri' => $baseUri,
    'headers'  => [
        'Authorization' => 'Bearer '.$apiToken,
        'Accept'        => 'application/json',
    ],
]);
$response = $client->get($challengePath);
$json     = json_decode($response->getBody());

while (true) {
    $currentHash = $json->hash;
    $target      = $json->target;

    echo "current hash: $currentHash, target: $target", PHP_EOL;

    sleep(1); // Rate limit

    do {
        $nounce         = bin2hex(random_bytes(5));
        $calculatedHash = hash('sha256', hash('sha256', $currentHash.$nounce));
    } while (strcmp($calculatedHash, $target) > 0);

    echo "nounce: $nounce, calculated hash: $calculatedHash", PHP_EOL;

    $response = $client->post($responsePath, [
        'json' => [
            'nounce' => $nounce,
        ],
    ]);
    $json = json_decode($response->getBody());
}
