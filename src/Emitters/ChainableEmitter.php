<?php

declare(strict_types=1);

namespace TijmenWierenga\SnowplowTracker\Emitters;

/**
 * @author Tijmen Wierenga <tijmen.wierenga@persgroep.net>
 */
final class ChainableEmitter implements Emitter
{
    /** @var Emitter[] */
    private array $emitters;

    public function __construct(Emitter ...$emitters)
    {
        $this->emitters = $emitters;
    }

    public function send(Payload $payload): void
    {
        foreach ($this->emitters as $emitter) {
            $emitter->send($payload);
        }
    }
}
