<?php

declare(strict_types=1);

namespace TijmenWierenga\SnowplowTracker\Events;

use JsonSerializable;
use TijmenWierenga\SnowplowTracker\Schemas\Schema;

/**
 * @author Tijmen Wierenga <t.wierenga@live.nl>
 */
interface Schemable
{
    public function getSchema(): Schema;
    public function getData(): array|string|int|float|bool|JsonSerializable;
}
