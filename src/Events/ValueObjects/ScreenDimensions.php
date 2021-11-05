<?php

declare(strict_types=1);

namespace TijmenWierenga\SnowplowTracker\Events\ValueObjects;

use Stringable;

/**
 * @author Tijmen Wierenga <t.wierenga@live.nl>
 */
final class ScreenDimensions implements Stringable
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

    public function toString(): string
    {
        return $this->width . 'x' . $this->height;
    }

    public function __toString(): string
    {
        return $this->toString();
    }
}
