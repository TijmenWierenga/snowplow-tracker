<?php

declare(strict_types=1);

namespace TijmenWierenga\SnowplowTracker\Events;

use Ramsey\Uuid\UuidInterface;

/**
 * @author Tijmen Wierenga <tijmen.wierenga@persgroep.net>
 */
final class TransactionItem extends Event
{
    public function __construct(
        public string $orderId,
        public string $sku,
        public int|float $price,
        public int $quantity,
        public ?string $currency = null,
        public ?string $name = null,
        public ?string $category = null,
        ?UuidInterface $id = null,
    ) {
        parent::__construct($id);
    }

    public function getType(): EventType
    {
        return EventType::ECOMMERCE_TRANSACTION_ITEM;
    }
}
