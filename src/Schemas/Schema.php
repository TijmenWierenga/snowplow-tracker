<?php

declare(strict_types=1);

namespace TijmenWierenga\SnowplowTracker\Schemas;

use Stringable;

/**
 * @author Tijmen Wierenga <t.wierenga@live.nl>
 */
class Schema implements Stringable
{
    public function __construct(
        public readonly string $vendor,
        public readonly string $name,
        public readonly Version $version,
        public readonly SchemaType $type = SchemaType::JSON_SCHEMA,
        public readonly SchemaRepository $schemaRepository = SchemaRepository::IGLU
    ) {
    }

    public function __toString(): string
    {
        return sprintf(
            '%s:%s/%s/%s/%s',
            $this->schemaRepository->value,
            $this->vendor,
            $this->name,
            $this->type->value,
            (string) $this->version
        );
    }
}
