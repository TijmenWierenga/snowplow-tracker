<?php

use TijmenWierenga\SnowplowTracker\Config\Platform;
use TijmenWierenga\SnowplowTracker\Config\TrackerConfig;
use TijmenWierenga\SnowplowTracker\Emitters\HttpClientEmitter;
use TijmenWierenga\SnowplowTracker\Events\StructuredEvent;
use TijmenWierenga\SnowplowTracker\Events\UnstructuredEvent;
use TijmenWierenga\SnowplowTracker\Schemas\Schema;
use TijmenWierenga\SnowplowTracker\Schemas\Version;
use TijmenWierenga\SnowplowTracker\Tracker;

beforeEach(function () {
    $this->httpClient = createClient();
    $this->tracker = new Tracker(
        new HttpClientEmitter($this->httpClient)
    );

    $this->httpClient->request('GET', '/micro/reset');
});

it('sends a structured event', function () {
    $event = new StructuredEvent(
        'a-category',
        'an-action'
    );

    $this->tracker->track($event);

    expect(successfulEvents())->toBe(1);
});

it('sends an unstructured event', function () {
    $event = new UnstructuredEvent(
        new Schema(
            'com.snowplowanalytics.snowplow',
            'ad_impression',
            new Version(1, 0, 0)
        ),
        [
            'impressionId' => '100'
        ]
    );

    $this->tracker->track($event);

    expect(successfulEvents())->toBe(1);
});

function successfulEvents(): int
{
    $response = createClient()->request('GET', '/micro/all');

    return json_decode($response->getContent(true), true, 512, JSON_THROW_ON_ERROR)['good'];
}
