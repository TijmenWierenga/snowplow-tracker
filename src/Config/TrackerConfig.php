<?php

declare(strict_types=1);

namespace TijmenWierenga\SnowplowTracker\Config;

/**
 * @author Tijmen Wierenga <t.wierenga@live.nl>
 */
class TrackerConfig
{
    public function __construct(
        public readonly Platform $platform = Platform::SERVER_SIDE_APP,
        public readonly ?string $trackerName = null,
        public readonly ?string $appId = null,
    ) {
    }

    public function getTrackerVersion(): string
    {
        return 'tijmenwierenga/snowplow-tracker';
    }
}
