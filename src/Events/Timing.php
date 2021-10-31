<?php

declare(strict_types=1);

namespace TijmenWierenga\SnowplowTracker\Events;

use DateTimeImmutable;
use DateTimeZone;

/**
 * @author Tijmen Wierenga <t.wierenga@live.nl>
 */
trait Timing
{
    public ?DateTimeImmutable $occuredAtClientDevice = null;
    public ?DateTimeImmutable $occuredAt = null;
    public ?DateTimeZone $timeZone = null;
}
