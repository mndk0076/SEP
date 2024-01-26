<?php

require 'vendor/autoload.php';
use Google\Analytics\Data\V1beta\Client\BetaAnalyticsDataClient;
use Google\Analytics\Data\V1beta\DateRange;
use Google\Analytics\Data\V1beta\Dimension;
use Google\Analytics\Data\V1beta\Metric;
use Google\Analytics\Data\V1beta\RunReportRequest;
use Google\Analytics\Data\V1beta\MinuteRanges;
use Google\Analytics\Data\V1beta\RunRealtimeReportRequest;
use LucidFrame\Console\ConsoleTable;

$property_id = '424493020';

// specified in GOOGLE_APPLICATION_CREDENTIALS environment variable.
$client = new BetaAnalyticsDataClient();

// REALTIME REPORT
$realtimeRequest = (new RunRealtimeReportRequest())
    ->setProperty('properties/' . $property_id)
    ->setDimensions([
        new Dimension(['name' => 'country']),
        new Dimension(['name' => 'city']),
        new Dimension(['name' => 'platform']),
    ])
    ->setMetrics([
        new Metric(['name' => 'activeUsers']),
        new Metric(['name' => 'eventCount']),
    ]);

$realtimeResponse = $client->runRealtimeReport($realtimeRequest);

// Print results of real-time API call.
print 'Real-time Report result: ' . PHP_EOL;

$table = new LucidFrame\Console\ConsoleTable();
$table->addHeader('Country')
    ->addHeader('City')
    ->addHeader('Platform')
    ->addHeader('Active Users')
    ->addHeader('Event Count');

foreach ($realtimeResponse->getRows() as $row) {
    $country = $row->getDimensionValues()[0]->getValue();
    $city = $row->getDimensionValues()[1]->getValue();
    $platform = $row->getDimensionValues()[2]->getValue();

    $activeUsers = $row->getMetricValues()[0]->getValue();
    $eventCount = $row->getMetricValues()[1]->getValue();

    $table->addRow()
        ->addColumn($country)
        ->addColumn($city)
        ->addColumn($platform)
        ->addColumn($activeUsers)
        ->addColumn($eventCount);
}

$table->display();

// DATE REPORT
$request = (new RunReportRequest())
    ->setProperty('properties/' . $property_id)
    ->setDateRanges([
        new DateRange([
            'start_date' => '2024-01-01',
            'end_date' => 'today',
        ]),
    ])
    ->setDimensions([
        new Dimension(['name' => 'country']),
        new Dimension(['name' => 'city']),
        new Dimension(['name' => 'sessionDefaultChannelGroup']),
        new Dimension(['name' => 'platform']),
        // new Dimension(['name' => 'eventName']),
    ])
    ->setMetrics([
        new Metric(['name' => 'screenPageViews']),
        new Metric(['name' => 'activeUsers']),
        new Metric(['name' => 'userEngagementDuration']),
        new Metric(['name' => 'eventCount']),
    ]); 


$response = $client->runReport($request);

// Print results of an API call.
print PHP_EOL . 'Report result: ' . PHP_EOL;

$table = new LucidFrame\Console\ConsoleTable();
$table->addHeader('Country')
        ->addHeader('City')
        ->addHeader('User From')
        ->addHeader('Platform')
        ->addHeader('Page Views')
        ->addHeader('Active Users')
        ->addHeader('Engagement Duration')
        ->addHeader('Event Count');

foreach ($response->getRows() as $row) {
    $country = $row->getDimensionValues()[0]->getValue();
    $city = $row->getDimensionValues()[1]->getValue();
    $defaultChannelGroup = $row->getDimensionValues()[2]->getValue();
    $platform = $row->getDimensionValues()[3]->getValue();
    // $eventName = $row->getDimensionValues()[1]->getValue();
    // $activeUsers = $row->getMetricValues()[0]->getValue();
    $screenPageViews = $row->getMetricValues()[0]->getValue();
    $activeUsers = $row->getMetricValues()[1]->getValue();
    $userEngagementDuration = $row->getMetricValues()[2]->getValue() . ' secs';
    $eventCount = $row->getMetricValues()[3]->getValue();
    
    $table->addRow()
        ->addColumn($country)
        ->addColumn($city)
        ->addColumn($defaultChannelGroup)
        ->addColumn($platform)
        ->addColumn($screenPageViews)
        ->addColumn($activeUsers)
        ->addColumn($userEngagementDuration)
        ->addColumn($eventCount);
        // ->addColumn($activeUsers);
}

$table->display();
?>
