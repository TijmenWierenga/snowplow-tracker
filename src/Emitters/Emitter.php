<?php

declare(strict_types=1);

namespace TijmenWierenga\SnowplowTracker\Emitters;

/**
 * @author Tijmen Wierenga <tijmen.wierenga@live.nl>
 *
 * Emits the event to a Snowplow collector URI
 */
interface Emitter
{
    public function send(Payload $payload): void;
}
