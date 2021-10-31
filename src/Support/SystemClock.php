<?php

declare(strict_types=1);

namespace TijmenWierenga\SnowplowTracker\Support;

use DateTimeImmutable;

/**
 * @author Tijmen Wierenga <tijmen.wierenga@persgroep.net>
 */
class SystemClock implements Clock
{
    public function now(): DateTimeImmutable
    {
        return new DateTimeImmutable();
    }
}
