<?php

declare(strict_types=1);

namespace TijmenWierenga\SnowplowTracker\Schemas;

use Stringable;

/**
 * @author Tijmen Wierenga <t.wierenga@live.nl>
 */
class Version implements Stringable
{
    public function __construct(
        public readonly int $major,
        public readonly int $minor,
        public readonly int $patch
    ) {
    }

    public function __toString(): string
    {
        return implode('-', [$this->major, $this->minor, $this->patch]);
    }
}
