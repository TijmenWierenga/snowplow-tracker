<?php

declare(strict_types=1);

namespace TijmenWierenga\SnowplowTracker\Emitters;

use RuntimeException;
use Throwable;

/**
 * @author Tijmen Wierenga <tijmen.wierenga@persgroep.net>
 */
final class FailedToEmit extends RuntimeException
{

    public function __construct(
        public readonly Payload $payload,
        string $message,
        int $code,
        ?Throwable $previous
    ) {
        parent::__construct($message, $code, $previous);
    }

    public static function withPayload(Payload $payload, int $code = 0, ?Throwable $previous = null): self
    {
        return new self(
            $payload,
            'Payload could not be emitted',
            $code,
            $previous
        );
    }
}
