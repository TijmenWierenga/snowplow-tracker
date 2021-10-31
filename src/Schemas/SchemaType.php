<?php

declare(strict_types=1);

namespace TijmenWierenga\SnowplowTracker\Schemas;

/**
 * @author Tijmen Wierenga <tijmen.wierenga@persgroep.net>
 */
enum SchemaType: string
{
    case JSON_SCHEMA = 'jsonschema';
}
