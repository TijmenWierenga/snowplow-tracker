<?php

declare(strict_types=1);

namespace TijmenWierenga\Tests\SnowplowTrackers\Emitters;

use DateTimeImmutable;
use Exception;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientExceptionInterface;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Psr18Client;
use Symfony\Component\HttpClient\Response\MockResponse;
use TijmenWierenga\SnowplowTracker\Config\TrackerConfig;
use TijmenWierenga\SnowplowTracker\Emitters\FailedToEmit;
use TijmenWierenga\SnowplowTracker\Emitters\HttpClientEmitter;
use TijmenWierenga\SnowplowTracker\Emitters\Payload;
use TijmenWierenga\SnowplowTracker\Events\StructuredEvent;

/**
 * @author Tijmen Wierenga <tijmen.wierenga@persgroep.net>
 */
final class HttpClientEmitterTest extends TestCase
{
    public function testItSendsThePayloadToTheCollector(): void
    {
        $mockResponse = new MockResponse();
        $mockHttpClient = new MockHttpClient($mockResponse);
        $client = new Psr18Client($mockHttpClient);

        $emitter = new HttpClientEmitter('http://localhost', $client);

        $config = new TrackerConfig();
        $event = new StructuredEvent('my-category', 'my-action');
        $payload = new Payload($config, $event, new DateTimeImmutable('2022-05-03T00:00:00'));

        $emitter->send($payload);

        self::assertEquals('POST', $mockResponse->getRequestMethod());
        self::assertEquals('http://localhost/com.snowplowanalytics.snowplow/tp2', $mockResponse->getRequestUrl());

        $body = (string) $mockResponse->getRequestOptions()['body'];
        self::assertEquals(
            [
                'schema' => 'iglu:com.snowplowanalytics.snowplow/payload_data/jsonschema/1-0-4',
                'data' => [$payload->asArray()]
            ],
            json_decode($body, true, 512, JSON_THROW_ON_ERROR)
        );
    }

    public function testItThrowsWhenResponseIsNot2xx(): void
    {
        $mockResponse = new MockResponse(info: ['http_code' => 500]);
        $mockHttpClient = new MockHttpClient($mockResponse);
        $client = new Psr18Client($mockHttpClient);

        $emitter = new HttpClientEmitter('http://localhost', $client);

        $config = new TrackerConfig();
        $event = new StructuredEvent('my-category', 'my-action');
        $payload = new Payload($config, $event, new DateTimeImmutable('2022-05-03T00:00:00'));

        $this->expectException(FailedToEmit::class);

        $emitter->send($payload);
    }

    public function testItCatchesAndRethrowsClientExceptions(): void
    {
        $mockHttpClient = new MockHttpClient(
            fn () => throw new class () extends Exception implements ClientExceptionInterface {}
        );
        $client = new Psr18Client($mockHttpClient);

        $emitter = new HttpClientEmitter('http://localhost', $client);

        $config = new TrackerConfig();
        $event = new StructuredEvent('my-category', 'my-action');
        $payload = new Payload($config, $event, new DateTimeImmutable('2022-05-03T00:00:00'));

        $this->expectException(FailedToEmit::class);

        $emitter->send($payload);
    }
}
