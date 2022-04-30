<?php

declare(strict_types=1);

namespace TijmenWierenga\SnowplowTracker\Emitters;

/**
 * @author Tijmen Wierenga <tijmen.wierenga@persgroep.net>
 */
interface FallbackStrategy
{
    /**
     * Recover when an event failed emitting
     */
    public function recover(Payload $payload): void;
}
