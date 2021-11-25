<?php

declare(strict_types=1);

namespace TijmenWierenga\Tests\SnowplowTrackers;

use Nyholm\Psr7\Request;
use Psr\Http\Client\ClientInterface;
use RuntimeException;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\Psr18Client;

/**
 * @author Tijmen Wierenga <tijmen.wierenga@persgroep.net>
 */
trait SnowplowMicroTestingUtils
{
    private static ?ClientInterface $httpClient = null;

    private function getSnowplowMicroClient(): ClientInterface
    {
        if (! self::$httpClient) {
            self::$httpClient = new Psr18Client(HttpClient::createForBaseUri($this->getSnowplowMicroBaseUri()));
        }

        return self::$httpClient;
    }

    private function resetEvents(): void
    {
        $this->getSnowplowMicroClient()->sendRequest(new Request('GET', '/micro/reset'));
    }

    private function getGoodEventsCount(): int
    {
        return $this->getEventCounter()['good'];
    }

    /**
     * @return array{good: int, bad: int, all: int}
     */
    private function getEventCounter(): array
    {
        /** @var array{good: int, bad: int, all: int} $result */
        $result = json_decode(
            (string) $this->getSnowplowMicroClient()->sendRequest(new Request('GET', '/micro/all'))->getBody(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        return $result;
    }

    private function getLatestEvent(): array
    {
        /** @var array<int, array{event: array<string, mixed>}> $result */
        $result = json_decode(
            (string) $this->getSnowplowMicroClient()->sendRequest(new Request('GET', '/micro/good'))->getBody(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        if (empty($result)) {
            throw new RuntimeException('Requested the latest event fails since event has been registered');
        }

        return $result[array_key_first($result)]['event'];
    }

    private function getSnowplowMicroBaseUri(): string
    {
        return 'http://snowplow_micro:9090';
    }
}
