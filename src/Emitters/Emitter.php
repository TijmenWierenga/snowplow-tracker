<?php

declare(strict_types=1);

namespace TijmenWierenga\SnowplowTracker\Emitters;

/**
 * @author Tijmen Wierenga <t.wierenga@live.nl>
 *
 * Emits the event to a Snowplow collector URI
 */
interface Emitter
{
    /**
     * @throws FailedToEmit Thrown when the payload could not be sent
     */
    public function send(Payload $payload): void;
}
