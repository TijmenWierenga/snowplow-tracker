<?php

declare(strict_types=1);

namespace TijmenWierenga\SnowplowTracker\Events;

/**
 * @author Tijmen Wierenga <tijmen.wierenga@live.nl>
 */
enum EventType: string
{
    case PAGEVIEW_TRACKING = 'pv';
    case PAGE_PINGS = 'pp';
    case ECOMMERCE_TRANSACTION_TRACKING = 'tr';
    case CUSTOM_STRUCTURED_EVENT = 'se';
    case CUSTOM_UNSTRUCTURED_EVENT = 'ue';
}
