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
    use Timing;
    use User;
    use Web;

    private UuidInterface $id;

    protected function __construct(
        ?UuidInterface $id = null
    ) {
        $this->id = $id ?? Uuid::uuid4();
    }

    abstract public function getType(): EventType;

    public function getId(): UuidInterface
    {
        return $this->id;
    }
}
