<?php

declare(strict_types=1);

namespace TijmenWierenga\SnowplowTracker\Events;

use Ramsey\Uuid\Nonstandard\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * @author Tijmen Wierenga <t.wierenga@live.nl>
 */
abstract class Event
{
    use Device;
    use IoT;
    use Timing;
    use User;
    use Web;

    public readonly UuidInterface $id;
    /** @var array<array-key, Schemable> */
    private array $contexts = [];

    protected function __construct(
        ?UuidInterface $id = null
    ) {
        $this->id = $id ?? Uuid::uuid4();
    }

    abstract public function getType(): EventType;

    public function addContext(Schemable $context): void
    {
        $this->contexts[] = $context;
    }

    public function getContexts(): array
    {
        return $this->contexts;
    }
}
