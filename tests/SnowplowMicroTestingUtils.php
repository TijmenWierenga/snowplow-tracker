<?php

declare(strict_types=1);

namespace TijmenWierenga\Tests\SnowplowTrackers;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * @author Tijmen Wierenga <tijmen.wierenga@persgroep.net>
 */
trait SnowplowMicroTestingUtils
{
    private static ?HttpClientInterface $httpClient = null;

    private function getSnowplowMicroClient(): HttpClientInterface
    {
        if (! self::$httpClient) {
            self::$httpClient = HttpClient::createForBaseUri('http://snowplow_micro:9090');
        }

        return self::$httpClient;
    }

    private function resetEvents(): void
    {
        $this->getSnowplowMicroClient()->request('GET', 'micro/reset');
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
        return json_decode(
            $this->getSnowplowMicroClient()->request('GET', '/micro/all')->getContent(true),
            true,
            512,
            JSON_THROW_ON_ERROR
        );
    }
}
