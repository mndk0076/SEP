<?php

require 'vendor/autoload.php';

use Google\Auth\Credentials\ServiceAccountCredentials;
use GuzzleHttp\Client;
use LucidFrame\Console\ConsoleTable;

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
    ],
    "dataState" => "ALL"
    // "rowLimit" => 25000
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

$totalClicks = 0;

$table = new ConsoleTable();
$table
    ->addHeader('Date')
    ->addHeader('Clicks');

foreach ($data as $row) {
    $totalClicks += $row['clicks'];

    $table->addRow()
        ->addColumn($row['keys'][0]) // Date
        ->addColumn($row['clicks']); // Clicks
}

$table->addRow()
    ->addColumn("-------------")
    ->addColumn("----");
$table->addRow()
    ->addColumn("TOTAL CLICKS")
    ->addColumn($totalClicks);

$table->display();

?>