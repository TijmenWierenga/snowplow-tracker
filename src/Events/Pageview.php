<?php

declare(strict_types=1);

namespace TijmenWierenga\SnowplowTracker\Events;

use Ramsey\Uuid\UuidInterface;

/**
 * @author Tijmen Wierenga <tijmen.wierenga@persgroep.net>
 */
final class Pageview extends Event
{
    public function __construct(
        string $url,
        ?string $pageTitle = null,
        ?string $referrer = null,
        ?UuidInterface $id = null
    ) {
        parent::__construct($id);

        $this->url = $url;
        $this->pageTitle = $pageTitle;
        $this->referrer = $referrer;
    }

    public function getType(): EventType
    {
        return EventType::PAGEVIEW_TRACKING;
    }
}
