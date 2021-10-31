<?php

declare(strict_types=1);

namespace TijmenWierenga\SnowplowTracker\Schemas;

/**
 * @author Tijmen Wierenga <tijmen.wierenga@persgroep.net>
 */
enum SchemaRepository: string
{
    case IGLU = 'iglu';
}
