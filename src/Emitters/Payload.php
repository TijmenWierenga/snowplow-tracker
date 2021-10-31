<?php

declare(strict_types=1);

namespace TijmenWierenga\SnowplowTracker\Emitters;

use DateTimeImmutable;
use JsonSerializable;
use TijmenWierenga\SnowplowTracker\Config\TrackerConfig;
use TijmenWierenga\SnowplowTracker\Events\Event;
use TijmenWierenga\SnowplowTracker\Events\StructuredEvent;
use TijmenWierenga\SnowplowTracker\Events\UnstructuredEvent;

/**
 * @author Tijmen Wierenga <tijmen.wierenga@live.nl>
 */
class Payload implements JsonSerializable
{
    private const TIMESTAMP_IN_MILLISECONDS = 'Uv';

    public function __construct(
        public readonly TrackerConfig $trackerConfig,
        public readonly Event $event,
        public readonly DateTimeImmutable $sentToCollector
    ) {
    }

    public function asArray(): array
    {
        return array_filter([
            // Common parameters
            'tna' => $this->trackerConfig->trackerName,
            'aid' => $this->trackerConfig->appId,
            'p' => $this->trackerConfig->platform->value,

            // Timing parameters
            'dtm' => $this->event->occuredAtClientDevice?->format(self::TIMESTAMP_IN_MILLISECONDS),
            'stm' => $this->sentToCollector->format(self::TIMESTAMP_IN_MILLISECONDS),
            'ttm' => $this->event->occuredAt?->format(self::TIMESTAMP_IN_MILLISECONDS),
            'tz' => $this->event->timeZone?->getName(),

            // Event parameters
            'e' => $this->event->getType()->value,
            'eid' => $this->event->getId()->toString(),

            // Tracker version
            'tv' => $this->trackerConfig->getTrackerVersion(),

            // User parameters
            'duid' => $this->event->domainUserId,
            'tnuid' => $this->event->networkUserId,
            'uid' => $this->event->userId,
            'sid' => $this->event->sessionId,
            'vid' => $this->event->sessionIdIndex,
            'ip' => $this->event->ipAddress,

            // Event parameters
            ...$this->mapEventSpecificFields()
        ]);
    }

    public function jsonSerialize(): array
    {
        return $this->asArray();
    }

    private function mapEventSpecificFields(): array
    {
        return match (true) {
            $this->event instanceof StructuredEvent => [
                'se_ca' => $this->event->category,
                'se_ac' => $this->event->action,
                'se_la' => $this->event->label,
                'se_pr' => $this->event->property,
                'se_va' => $this->event->value
            ],
            $this->event instanceof UnstructuredEvent => [
                'ue_pr' => json_encode(
                    [
                        'schema' => 'iglu:com.snowplowanalytics.snowplow/unstruct_event/jsonschema/1-0-0',
                        'data' => [
                            'schema' => (string) $this->event->schema,
                            'data' => $this->event->data,
                        ]
                    ],
                    JSON_THROW_ON_ERROR
                )
            ],
        };
    }
}
