<?php

declare(strict_types=1);

namespace TijmenWierenga\SnowplowTracker\Emitters;

/**
 * @author Tijmen Wierenga <tijmen.wierenga@persgroep.net>
 */
final class VoidFallbackStrategy implements FallbackStrategy
{
    public function recover(Payload $payload): void
    {
    }
}
