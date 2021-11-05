<?php

declare(strict_types=1);

namespace TijmenWierenga\SnowplowTracker\Emitters;

use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * @author Tijmen Wierenga <t.wierenga@live.nl>
 */
class HttpClientEmitter implements Emitter
{
    private const POST_EVENT_URL = '/com.snowplowanalytics.snowplow/tp2';
    private const CURRENT_JSON_SCHEMA = 'iglu:com.snowplowanalytics.snowplow/payload_data/jsonschema/1-0-4';

    public function __construct(
        private HttpClientInterface $httpClient
    ) {
    }

    public function send(Payload $payload): void
    {
        $this->httpClient->request(
            'POST',
            self::POST_EVENT_URL,
            [
                'json' => [
                    'schema' => self::CURRENT_JSON_SCHEMA,
                    'data' => [$payload->asArray()]
                ]
            ]
        );
    }
}
