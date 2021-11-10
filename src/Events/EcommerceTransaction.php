<?php

declare(strict_types=1);

namespace TijmenWierenga\SnowplowTracker\Events;

use Ramsey\Uuid\UuidInterface;

/**
 * @author Tijmen Wierenga <t.wierenga@live.nl>
 */
final class EcommerceTransaction extends Event
{
    /**
     * @param TransactionItem[] $items
     */
    public function __construct(
        public string $orderId,
        public int|float $totalValue,
        public ?string $currency = null,
        public ?string $affiliation = null,
        public int|float|null $taxValue = null,
        public int|float|null $deliveryCosts = null,
        public ?string $deliveryCity = null,
        public ?string $deliveryState = null,
        public ?string $deliveryCountry = null,
        ?UuidInterface $id = null
    ) {
        parent::__construct($id);
    }

    public function getType(): EventType
    {
        return EventType::ECOMMERCE_TRANSACTION_TRACKING;
    }
}
