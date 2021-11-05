<?php

declare(strict_types=1);

namespace TijmenWierenga\SnowplowTracker\Support\Time;

use DateTimeImmutable;

/**
 * @author Tijmen Wierenga <t.wierenga@live.nl>
 */
class SystemClock implements Clock
{
    public function now(): DateTimeImmutable
    {
        return new DateTimeImmutable();
    }
}
