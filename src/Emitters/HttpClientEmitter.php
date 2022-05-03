<?php

declare(strict_types=1);

namespace TijmenWierenga\SnowplowTracker\Emitters;

use Http\Discovery\Psr17FactoryDiscovery;
use Http\Discovery\Psr18ClientDiscovery;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;

/**
 * @author Tijmen Wierenga <t.wierenga@live.nl>
 */
final class HttpClientEmitter implements Emitter
{
    private const POST_EVENT_URL = '/com.snowplowanalytics.snowplow/tp2';
    private const CURRENT_JSON_SCHEMA = 'iglu:com.snowplowanalytics.snowplow/payload_data/jsonschema/1-0-4';
    private const SUCCESSFUL_STATUS_CODES = [200, 201, 204];

    private readonly ClientInterface $httpClient;
    private readonly RequestFactoryInterface $requestFactory;

    public function __construct(
        private readonly string $collectorUri,
        ?ClientInterface $httpClient = null,
        ?RequestFactoryInterface $requestFactory = null
    ) {
        $this->httpClient = $httpClient ?? Psr18ClientDiscovery::find();
        $this->requestFactory = $requestFactory ?? Psr17FactoryDiscovery::findRequestFactory();
    }

    public function send(Payload $payload): void
    {
        $request = $this->requestFactory->createRequest('POST', $this->collectorUri . self::POST_EVENT_URL)
            ->withHeader('Content-Type', 'application/json');

        $request->getBody()->write(json_encode(
            [
                'schema' => self::CURRENT_JSON_SCHEMA,
                'data' => [$payload->asArray()]
            ],
            JSON_THROW_ON_ERROR
        ));

        try {
            $response = $this->httpClient->sendRequest($request);

            if (! in_array($response->getStatusCode(), self::SUCCESSFUL_STATUS_CODES)) {
                throw FailedToEmit::withPayload($payload);
            }
        } catch (ClientExceptionInterface $e) {
            throw FailedToEmit::withPayload($payload, previous: $e);
        }
    }
}
