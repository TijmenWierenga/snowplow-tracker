<?php

declare(strict_types=1);

namespace TijmenWierenga\Tests\SnowplowTrackers;

use JsonSerializable;
use PHPUnit\Framework\TestCase;
use TijmenWierenga\SnowplowTracker\Emitters\DebugEmitter;
use TijmenWierenga\SnowplowTracker\Emitters\HttpClientEmitter;
use TijmenWierenga\SnowplowTracker\Events\EcommerceTransaction;
use TijmenWierenga\SnowplowTracker\Events\Event;
use TijmenWierenga\SnowplowTracker\Events\PagePing;
use TijmenWierenga\SnowplowTracker\Events\Pageview;
use TijmenWierenga\SnowplowTracker\Events\Schemable;
use TijmenWierenga\SnowplowTracker\Events\StructuredEvent;
use TijmenWierenga\SnowplowTracker\Events\TransactionItem;
use TijmenWierenga\SnowplowTracker\Events\UnstructuredEvent;
use TijmenWierenga\SnowplowTracker\Schemas\Schema;
use TijmenWierenga\SnowplowTracker\Schemas\Version;
use TijmenWierenga\SnowplowTracker\Tracker;

/**
 * @author Tijmen Wierenga <t.wierenga@live.nl>
 */
final class TrackerTest extends TestCase
{
    use SnowplowMicroTestingUtils;

    protected function setUp(): void
    {
        $this->resetEvents();
    }

    public function testItShouldEmitAStructuredEvent(): void
    {
        // Given I have a tracker
        $httpClient = $this->getSnowplowMicroClient();
        $emitter = new HttpClientEmitter($httpClient);
        $tracker = new Tracker($emitter);

        // When I track a structured event
        $tracker->track(new StructuredEvent('my-category', 'my-action'));

        // Then I expect the event to be successfully inserted
        self::assertEquals(1, $this->getGoodEventsCount());
    }

    public function testItShouldTrackAStructuredEvent(): void
    {
        // Given I have a tracker
        $httpClient = $this->getSnowplowMicroClient();
        $emitter = new HttpClientEmitter($httpClient);
        $tracker = new Tracker($emitter);

        // When I track a structured event
        $tracker->track(
            new UnstructuredEvent(
                new Schema(
                    'com.snowplowanalytics.snowplow',
                    'ad_impression',
                    new Version(1, 0, 0)
                ),
                [
                    'impressionId' => '105'
                ]
            )
        );

        // Then I expect the event to be successfully inserted
        self::assertEquals(1, $this->getGoodEventsCount());
    }

    public function testItShouldTrackAPageview(): void
    {
        // Given I have a tracker
        $httpClient = $this->getSnowplowMicroClient();
        $emitter = new HttpClientEmitter($httpClient);
        $tracker = new Tracker($emitter);

        // When I track a pageview
        $tracker->track(
            new Pageview(
                'https://github.com/TijmenWierenga',
                'My personal Github account',
                'https://twitter.com/TijmenWierenga'
            )
        );

        // Then I expect the event to be inserted successfully
        self::assertEquals(1, $this->getGoodEventsCount());
    }

    public function testItShouldTrackAPagePing(): void
    {
        // Given I have a tracker
        $httpClient = $this->getSnowplowMicroClient();
        $emitter = new HttpClientEmitter($httpClient);
        $tracker = new Tracker($emitter);

        // When I track a pageview
        $tracker->track(new PagePing(0, 100, 50, 250));

        // Then I expect the event to be inserted successfully
        self::assertEquals(1, $this->getGoodEventsCount());
    }

    public function testItShouldTrackAnEcommerceTransaction(): void
    {
        // Given I have a tracker
        $httpClient = $this->getSnowplowMicroClient();
        $emitter = new HttpClientEmitter($httpClient);
        $tracker = new Tracker($emitter);

        // When I track an Ecommerce transaction
        $tracker->track(new EcommerceTransaction(
            '6d69fc0c-8144-4a9e-a503-88da693f17a3',
            89.95,
            'EUR',
            'My Affiliation',
            3.50,
            4.95,
            'Amsterdam',
            'Noord-Holland',
            'Netherlands'
        ));

        // Then I expect the event to be inserted successfully
        self::assertEquals(1, $this->getGoodEventsCount());
    }

    public function testItShouldTrackATransactionItem(): void
    {
        // Given I have a tracker
        $httpClient = $this->getSnowplowMicroClient();
        $emitter = new HttpClientEmitter($httpClient);
        $tracker = new Tracker($emitter);

        // When I track an Ecommerce transaction
        $tracker->track(new TransactionItem(
            '6d69fc0c-8144-4a9e-a503-88da693f17a3',
            '580b9f55-f8d0-405a-93d4-56b4bf64d76b',
            50.05,
            4,
            'EUR',
            'Apple iPhone 13',
            'Smartphones'
        ));

        // Then I expect the event to be inserted successfully
        self::assertEquals(1, $this->getGoodEventsCount());
    }

    public function testItShouldBePassedToMiddleware(): void
    {
        // Given I have a tracker with middleware
        $emitter = new DebugEmitter();
        $tracker = new Tracker(
            $emitter,
            [
                function (Event $event): Event {
                    $event->userId = 't.wierenga@live.nl';

                    return $event;
                },
                function (Event $event): Event {
                    $event->sessionId = '707ac2fb-b4a8-422b-9f58-41e1fa79ce5a';

                    return $event;
                }
            ]
        );

        // When I emit an event
        $event = new StructuredEvent('my-category', 'my-action');
        $tracker->track($event);

        // Then I expect the middleware to have modified the event
        $payload = $emitter->getLatestPayload();
        self::assertEquals('t.wierenga@live.nl', $payload->event->userId);
        self::assertEquals('707ac2fb-b4a8-422b-9f58-41e1fa79ce5a', $payload->event->sessionId);
    }

    public function testItShouldAddCustomContext(): void
    {
        // Given I have a tracker
        $httpClient = $this->getSnowplowMicroClient();
        $emitter = new HttpClientEmitter($httpClient);
        $tracker = new Tracker($emitter);

        // When I track an event with custom context
        $event = new StructuredEvent('my-category', 'my-action');
        $event->addContext(
            new class () implements Schemable {
                public function getSchema(): Schema
                {
                    return new Schema('com.snowplowanalytics.snowplow', 'timing', new Version(1, 0, 0));
                }

                public function getData(): array|string|int|float|bool|JsonSerializable
                {
                    return [
                        'category' => 'demo',
                        'variable' => 'duration',
                        'timing' => 145
                    ];
                }
            }
        );
        $tracker->track($event);

        // Then I expect the event to be inserted successfully
        self::assertEquals(1, $this->getGoodEventsCount());
    }
}
