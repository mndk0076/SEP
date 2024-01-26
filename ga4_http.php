<?php

require 'vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use LucidFrame\Console\ConsoleTable;
use Google\Auth\Credentials\ServiceAccountCredentials;

$serviceAccountPath = __DIR__ . '/search-engine-people-65757356316c.json';

// Set up the service account credentials
$credentials = new ServiceAccountCredentials(
    ['https://www.googleapis.com/auth/analytics.readonly'], // Use analytics scope
    $serviceAccountPath
);

try {
    $accessToken = $credentials->fetchAuthToken()['access_token']; 

    // Set up GuzzleHttp client
    $client = new Client([
        'base_uri' => 'https://analyticsdata.googleapis.com/v1beta/',
    ]);

    $property_id = '424493020';

    // REALTIME REPORT
    $realtimeRequest = [
        'dimensions' => [
            ['name' => 'country'],
            ['name' => 'city'],
            ['name' => 'platform'],
        ],
        'metrics' => [
            ['name' => 'activeUsers'],
            ['name' => 'eventCount'],
        ],
    ];

    $realtimeResponse = $client->post('properties/' . $property_id . ':runRealtimeReport', [
        'headers' => [
            'Authorization' => 'Bearer ' . $accessToken,
            'Content-Type' => 'application/json',
        ],
        'json' => [
            'dimensions' => $realtimeRequest['dimensions'],
            'metrics' => $realtimeRequest['metrics'],
        ],
    ])->getBody()->getContents();

    $realtimeResponse = json_decode($realtimeResponse, true);


    // Print results of real-time API call.
    print 'Real-time Report result: ' . PHP_EOL;

    $table = new ConsoleTable();
    $table->addHeader('Country')
        ->addHeader('City')
        ->addHeader('Platform')
        ->addHeader('Active Users')
        ->addHeader('Event Count');

    foreach ($realtimeResponse['rows'] as $row) {
        $country = $row['dimensionValues'][0]['value'];
        $city = $row['dimensionValues'][1]['value'];
        $platform = $row['dimensionValues'][2]['value'];

        $activeUsers = $row['metricValues'][0]['value'];
        $eventCount = $row['metricValues'][1]['value'];

        $table->addRow()
            ->addColumn($country)
            ->addColumn($city)
            ->addColumn($platform)
            ->addColumn($activeUsers)
            ->addColumn($eventCount);
    }

    $table->display();


    // DATE REPORT
    $dateRequest = [
        'dateRanges' => [
            'startDate' => '2024-01-01',
            'endDate' => 'today',
        ],
        'dimensions' => [
            ['name' => 'country'],
            ['name' => 'city'],
            ['name' => 'sessionDefaultChannelGroup'],
            ['name' => 'platform'],
        ],
        'metrics' => [
            ['name' => 'screenPageViews'],
            ['name' => 'activeUsers'],
            ['name' => 'userEngagementDuration'],
            ['name' => 'eventCount'],
        ],
    ];
    

    $dateResponse = $client->post('properties/' . $property_id . ':runReport', [
        'headers' => [
            'Authorization' => 'Bearer ' . $accessToken,
            'Content-Type' => 'application/json',
        ],
        'json' => [
            'dimensions' => $dateRequest['dimensions'],
            'dateRanges' => [$dateRequest['dateRanges']],
            'metrics' => $dateRequest['metrics'],
        ],
    ])->getBody()->getContents();

    $dateResponse = json_decode($dateResponse, true);

    // var_dump($dateResponse);

    // Print results 
    print  PHP_EOL . 'Date Report result: ' . PHP_EOL;

    $table = new ConsoleTable();
    $table->addHeader('Country')
        ->addHeader('City')
        ->addHeader('Session From')
        ->addHeader('Platform')
        ->addHeader('Page Views')
        ->addHeader('Active Users')
        ->addHeader('User Engagement Duration')
        ->addHeader('Event Count');

    foreach ($dateResponse['rows'] as $row) {
        $country = $row['dimensionValues'][0]['value'];
        $city = $row['dimensionValues'][1]['value'];
        $sessionDefaultChannelGroup = $row['dimensionValues'][2]['value'];
        $platform = $row['dimensionValues'][3]['value'];

        $screenPageViews = $row['metricValues'][0]['value'];
        $activeUsers = $row['metricValues'][1]['value'];
        $userEngagementDuration = $row['metricValues'][2]['value'] . ' secs';
        $eventCount = $row['metricValues'][3]['value'];

        $table->addRow()
            ->addColumn($country)
            ->addColumn($city)
            ->addColumn($sessionDefaultChannelGroup)
            ->addColumn($platform)
            ->addColumn($screenPageViews)
            ->addColumn($activeUsers)
            ->addColumn($userEngagementDuration)
            ->addColumn($eventCount);
    }

    $table->display();

} catch (RequestException $e) {
    // Handle the exception
    echo 'Error: ' . $e->getMessage();
}

?>
