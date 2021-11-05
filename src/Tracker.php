<?php

declare(strict_types=1);

namespace TijmenWierenga\SnowplowTracker;

use TijmenWierenga\SnowplowTracker\Config\TrackerConfig;
use TijmenWierenga\SnowplowTracker\Emitters\Emitter;
use TijmenWierenga\SnowplowTracker\Emitters\Payload;
use TijmenWierenga\SnowplowTracker\Events\Event;
use TijmenWierenga\SnowplowTracker\Support\Time\Clock;
use TijmenWierenga\SnowplowTracker\Support\Time\SystemClock;

/**
 * @author Tijmen Wierenga <t.wierenga@live.nl>
 */
class Tracker
{
    /**
     * @param array<array-key, callable(Event): Event> $middleware
     */
    public function __construct(
        private readonly Emitter $emitter,
        private readonly array $middleware = [],
        private readonly TrackerConfig $config = new TrackerConfig(),
        private readonly Clock $clock = new SystemClock()
    ) {
    }

    public function track(Event $event): void
    {
        foreach ($this->middleware as $middleware) {
            $event = $middleware($event);
        }

        // Generate payload
        $payload = new Payload($this->config, $event, $this->clock->now());

        // Send to collector
        $this->emitter->send($payload);
    }
}
