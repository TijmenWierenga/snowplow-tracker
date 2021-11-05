<?php

declare(strict_types=1);

namespace TijmenWierenga\SnowplowTracker\Support\Time;

use DateTimeImmutable;

/**
 * @author Tijmen Wierenga <t.wierenga@live.nl>
 */
interface Clock
{
    public function now(): DateTimeImmutable;
}
