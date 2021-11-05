<?php

declare(strict_types=1);

namespace TijmenWierenga\SnowplowTracker\Events;

use Ramsey\Uuid\UuidInterface;

/**
 * @author Tijmen Wierenga <tijmen.wierenga@persgroep.net>
 */
final class PagePing extends Event
{
    public function __construct(
        public ?int $minimumHorizontalOffset = null,
        public ?int $maximumHorizontalOffset = null,
        public ?int $minimumVerticalOffset = null,
        public ?int $maximumVerticalOffset = null,
        ?UuidInterface $id = null
    ) {
        parent::__construct($id);
    }

    public function getType(): EventType
    {
        return EventType::PAGE_PINGS;
    }
}
