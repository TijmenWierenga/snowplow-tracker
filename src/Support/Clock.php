<?php

declare(strict_types=1);

namespace TijmenWierenga\SnowplowTracker\Support;

use DateTimeImmutable;

/**
 * @author Tijmen Wierenga <tijmen.wierenga@persgroep.net>
 */
interface Clock
{
    public function now(): DateTimeImmutable;
}
