<?php

declare(strict_types=1);

namespace TijmenWierenga\SnowplowTracker\Events;

use TijmenWierenga\SnowplowTracker\Events\ValueObjects\ScreenResolution;

/**
 * @author Tijmen Wierenga <t.wierenga@live.nl>
 */
trait Device
{
    public ?ScreenResolution $screenResolution = null;
}
