<?php

declare(strict_types=1);

namespace TijmenWierenga\SnowplowTracker\Support\Filters;

/**
 * @author Tijmen Wierenga <t.wierenga@live.nl>
 */
final class ExcludeNull
{
    public function __invoke(mixed $value): bool
    {
        return $value !== null;
    }
}
