<?php

declare(strict_types=1);

namespace TijmenWierenga\SnowplowTracker\Emitters;

use RuntimeException;

/**
 * @author Tijmen Wierenga <tijmen.wierenga@persgroep.net>
 */
final class DebugEmitter implements Emitter
{
    /** @var Payload[] */
    private array $payloads = [];

    public function send(Payload $payload): void
    {
        $this->payloads[] = $payload;
    }

    public function getPayloads(): array
    {
        return $this->payloads;
    }

    public function getLatestPayload(): Payload
    {
        if (empty($this->payloads)) {
            throw new RuntimeException('There are no emitted events');
        }

        return $this->payloads[array_key_last($this->payloads)];
    }
}
