<?php

declare(strict_types=1);

namespace TijmenWierenga\SnowplowTracker\Events;

/**
 * @author Tijmen Wierenga <t.wierenga@live.nl>
 */
trait User
{
    public ?string $domainUserId = null;
    public ?string $networkUserId = null;
    public ?string $userId = null;
    public ?string $ipAddress = null;
    public ?string $sessionId = null;
    public ?int $sessionIdIndex = null;
}
