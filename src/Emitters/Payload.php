<?php

declare(strict_types=1);

namespace TijmenWierenga\SnowplowTracker\Emitters;

use DateTimeImmutable;
use JsonSerializable;
use TijmenWierenga\SnowplowTracker\Config\TrackerConfig;
use TijmenWierenga\SnowplowTracker\Events\EcommerceTransaction;
use TijmenWierenga\SnowplowTracker\Events\Event;
use TijmenWierenga\SnowplowTracker\Events\PagePing;
use TijmenWierenga\SnowplowTracker\Events\Pageview;
use TijmenWierenga\SnowplowTracker\Events\Schemable;
use TijmenWierenga\SnowplowTracker\Events\StructuredEvent;
use TijmenWierenga\SnowplowTracker\Events\TransactionItem;
use TijmenWierenga\SnowplowTracker\Events\UnstructuredEvent;
use TijmenWierenga\SnowplowTracker\Support\Filters\ExcludeNull;

/**
 * @author Tijmen Wierenga <t.wierenga@live.nl>
 */
final class Payload implements JsonSerializable
{
    final public const TIMESTAMP_IN_MILLISECONDS = 'Uv';

    public function __construct(
        public readonly TrackerConfig $trackerConfig,
        public readonly Event $event,
        public readonly DateTimeImmutable $sentToCollector
    ) {
    }

    public function asArray(): array
    {
        return array_filter(
            [
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
                'eid' => $this->event->id->toString(),

                // Tracker version
                'tv' => $this->trackerConfig->getTrackerVersion(),

                // User parameters
                'duid' => $this->event->domainUserId,
                'tnuid' => $this->event->networkUserId,
                'uid' => $this->event->userId,
                'sid' => $this->event->sessionId,
                'vid' => $this->event->sessionIdIndex,
                'ip' => $this->event->ipAddress,

                // Device parameters
                'res' => $this->event->screenResolution?->toString(),

                // Web parameters
                'url' => $this->event->url,
                'ua' => $this->event->userAgent,
                'page' => $this->event->pageTitle,
                'refr' => $this->event->referrer,
                'fp' => $this->event->userFingerprint,
                'cookie' => $this->event->permitsCookies,
                'lang' => $this->event->browserLanguage,
                'f_pdf' => $this->event->adobePdfPluginInstalled,
                'f_qt' => $this->event->quicktimePluginInstalled,
                'f_realp' => $this->event->realplayerInstalled,
                'f_wma' => $this->event->windowsMediaPluginInstalled,
                'f_dir' => $this->event->directorPluginInstalled,
                'f_fla' => $this->event->flashPluginInstalled,
                'f_gears' => $this->event->googleGearsPluginInstalled,
                'f_ag' => $this->event->silverlightPluginInstalled,
                'cd' => $this->event->browserColorDept,
                'ds' => $this->event->webPageDimensions?->toString(),
                'cs' => $this->event->characterEncoding,
                'vp' => $this->event->browserViewportDimensions?->toString(),

                // Internet of Things parameters
                'mac' => $this->event->macAddress,

                // Custom context
                'cx' => $this->mapCustomContext(),

                // Event parameters
                ...$this->mapEventSpecificFields()
            ],
            new ExcludeNull()
        );
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
                'ue_px' => base64_encode(
                    json_encode(
                        [
                            'schema' => 'iglu:com.snowplowanalytics.snowplow/unstruct_event/jsonschema/1-0-0',
                            'data' => [
                                'schema' => (string) $this->event->schema,
                                'data' => $this->event->data,
                            ]
                        ],
                        JSON_THROW_ON_ERROR
                    )
                )
            ],
            $this->event instanceof Pageview => [],
            $this->event instanceof PagePing => [
                'pp_mix' => (string) $this->event->minimumHorizontalOffset,
                'pp_max' => (string) $this->event->maximumHorizontalOffset,
                'pp_miy' => (string) $this->event->minimumVerticalOffset,
                'pp_may' => (string) $this->event->maximumVerticalOffset,
            ],
            $this->event instanceof EcommerceTransaction => [
                'tr_id' => $this->event->orderId,
                'tr_af' => $this->event->affiliation,
                'tr_tt' => (string) $this->event->totalValue,
                'tr_tx' => (string) $this->event->taxValue,
                'tr_sh' => (string) $this->event->deliveryCosts,
                'tr_ci' => $this->event->deliveryCity,
                'tr_st' => $this->event->deliveryState,
                'tr_co' => $this->event->deliveryCountry,
                'tr_cu' => $this->event->currency,
            ],
            $this->event instanceof TransactionItem => [
                'ti_id' => $this->event->orderId,
                'ti_sk' => $this->event->sku,
                'ti_nm' => $this->event->name,
                'ti_ca' => $this->event->category,
                'ti_pr' => (string) $this->event->price,
                'ti_qu' => (string) $this->event->quantity,
                'ti_cu' => $this->event->currency
            ],
        };
    }

    private function mapCustomContext(): ?string
    {
        $contexts = $this->event->getContexts();

        if (empty($contexts)) {
            return null;
        }

        $schema = [
            'schema' => 'iglu:com.snowplowanalytics.snowplow/contexts/jsonschema/1-0-1',
            'data' => array_map(
                static fn (Schemable $context): array => [
                    'schema' => (string) $context->getSchema(),
                    'data' => $context->getData()
                ],
                $this->event->getContexts()
            )
        ];

        return base64_encode(json_encode($schema, JSON_THROW_ON_ERROR));
    }
}
