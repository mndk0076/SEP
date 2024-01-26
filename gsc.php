<?php

require 'vendor/autoload.php';

use Google\Auth\Credentials\ServiceAccountCredentials;
use GuzzleHttp\Client;

// Service account credentials
$serviceAccountPath = __DIR__ . '/search-engine-people-65757356316c.json';

// Set up the service account credentials
$credentials = new ServiceAccountCredentials([
    'https://www.googleapis.com/auth/webmasters' 
], $serviceAccountPath);

$accessToken = $credentials->fetchAuthToken();

// API endpoint
$apiEndpoint = 'https://www.googleapis.com/webmasters/v3/sites/sc-domain:kenmendoza.dev/searchAnalytics/query';

// Body request
$requestBody = [
    "startDate" => "2024-01-01",
    "endDate" => "2024-01-31",
    "dimensions" => [
        "DATE",
        "QUERY",
        "PAGE",
        "COUNTRY",
        "DEVICE",
    ],
    "dataState" => "ALL",
    "rowLimit" => 25000
];

// HTTP client
$client = new Client();

// API request
$response = $client->post($apiEndpoint, [
    'headers' => [
        'Authorization' => 'Bearer ' . $accessToken['access_token'],
        'Content-Type' => 'application/json',
    ],
    'json' => $requestBody,
]);

// Get the response body as JSON
$responseBody = json_decode($response->getBody(), true);

// Display the data in a table
$data = $responseBody['rows'];
$table = new LucidFrame\Console\ConsoleTable();
$table
    ->addHeader('DATE')
    ->addHeader('QUERY')
    ->addHeader('PAGE')
    ->addHeader('COUNTRY')
    ->addHeader('DEVICE')
    ->addHeader('Impressions')
    ->addHeader('Clicks')
    ->addHeader('CTR')
    ->addHeader('Position');

foreach ($data as $row) {
    $table->addRow()
        ->addColumn($row['keys'][0]) // DATE
        ->addColumn($row['keys'][1]) // QUERY
        ->addColumn($row['keys'][2]) // PAGE
        ->addColumn($row['keys'][3]) // COUNTRY
        ->addColumn($row['keys'][4]) // DEVICE
        ->addColumn($row['impressions']) // Impressions
        ->addColumn($row['clicks']) // Clicks
        ->addColumn($row['ctr']) // CTR
        ->addColumn($row['position']); // Position
}

$table->display();

?>