<?php

declare(strict_types=1);

namespace TijmenWierenga\SnowplowTracker\Events\ValueObjects;

use Stringable;

/**
 * @author Tijmen Wierenga <tijmen.wierenga@persgroep.net>
 */
final class ScreenResolution implements Stringable
{
    /**
     * @psalm-param positive-int $width
     * @psalm-param positive-int $height
     */
    public function __construct(
        public readonly int $width,
        public readonly int $height
    ) {
    }

    public function __toString(): string
    {
        return $this->width . 'x' . $this->height;
    }
}
