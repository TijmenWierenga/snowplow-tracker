<?php

declare(strict_types=1);

namespace TijmenWierenga\SnowplowTracker\Events;

use Ramsey\Uuid\UuidInterface;

/**
 * @author Tijmen Wierenga <tijmen.wierenga@live.nl>
 */
class StructuredEvent extends Event
{
    public function __construct(
        public string $category,
        public string $action,
        public ?string $label = null,
        public ?string $property = null,
        public int|float|null $value = null,
        ?UuidInterface $id = null
    ) {
        parent::__construct($id);
    }

    public function getType(): EventType
    {
        return EventType::CUSTOM_STRUCTURED_EVENT;
    }
}
