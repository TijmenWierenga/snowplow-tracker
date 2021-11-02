<?php

declare(strict_types=1);

namespace TijmenWierenga\Tests\SnowplowTrackers;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use TijmenWierenga\SnowplowTracker\Emitters\DebugEmitter;
use TijmenWierenga\SnowplowTracker\Emitters\HttpClientEmitter;
use TijmenWierenga\SnowplowTracker\Emitters\Payload;
use TijmenWierenga\SnowplowTracker\Events\Event;
use TijmenWierenga\SnowplowTracker\Events\StructuredEvent;
use TijmenWierenga\SnowplowTracker\Events\UnstructuredEvent;
use TijmenWierenga\SnowplowTracker\Schemas\Schema;
use TijmenWierenga\SnowplowTracker\Schemas\Version;
use TijmenWierenga\SnowplowTracker\Tracker;

/**
 * @author Tijmen Wierenga <tijmen.wierenga@persgroep.net>
 */
final class TrackerTest extends TestCase
{
    protected function setUp(): void
    {
        $this->createSnowplowMicroClient()->request('GET', '/micro/reset');
    }

    public function testItShouldEmitAStructuredEvent(): void
    {
        // Given I have a tracker
        $httpClient = $this->createSnowplowMicroClient();
        $emitter = new HttpClientEmitter($httpClient);
        $tracker = new Tracker($emitter);

        // When I track a structured event
        $tracker->track(new StructuredEvent('my-category', 'my-action'));

        // Then I expect the event to be successfully inserted
        /** @var array{good: int, bad: int, all: int} $events */
        $events = json_decode(
            $httpClient->request('GET', '/micro/all')->getContent(true),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        self::assertEquals(1, $events['good']);
    }

    public function testItShouldTrackAStructuredEvent(): void
    {
        // Given I have a tracker
        $httpClient = $this->createSnowplowMicroClient();
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
        /** @var array{good: int, bad: int, all: int} $events */
        $events = json_decode(
            $httpClient->request('GET', '/micro/all')->getContent(true),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        self::assertEquals(1, $events['good']);
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

    private function createSnowplowMicroClient(): HttpClientInterface
    {
        return HttpClient::createForBaseUri('http://snowplow_micro:9090');
    }
}
