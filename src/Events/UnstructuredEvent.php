<?php

declare(strict_types=1);

namespace TijmenWierenga\SnowplowTracker\Events;

use JsonSerializable;
use Ramsey\Uuid\UuidInterface;
use TijmenWierenga\SnowplowTracker\Schemas\Schema;

/**
 * @author Tijmen Wierenga <t.wierenga@live.nl>
 */
class UnstructuredEvent extends Event
{
    public function __construct(
        public readonly Schema $schema,
        public array|string|int|float|bool|JsonSerializable $data,
        ?UuidInterface $id = null
    )
    {
        parent::__construct($id);
    }

    public function getType(): EventType
    {
        return EventType::CUSTOM_UNSTRUCTURED_EVENT;
    }
}
