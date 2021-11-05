<?php

declare(strict_types=1);

namespace TijmenWierenga\SnowplowTracker\Events;

use TijmenWierenga\SnowplowTracker\Events\ValueObjects\ScreenDimensions;

/**
 * @author Tijmen Wierenga <t.wierenga@live.nl>
 */
trait Web
{
    public ?string $url = null;
    public ?string $userAgent = null;
    public ?string $pageTitle = null;
    public ?string $referrer = null;
    public ?int $userFingerprint = null;
    public ?bool $permitsCookies = null;
    public ?string $browserLanguage = null;
    public ?bool $adobePdfPluginInstalled = null;
    public ?bool $quicktimePluginInstalled = null;
    public ?bool $realplayerInstalled = null;
    public ?bool $windowsMediaPluginInstalled = null;
    public ?bool $directorPluginInstalled = null;
    public ?bool $flashPluginInstalled = null;
    public ?bool $javaPluginInstalled = null;
    public ?bool $googleGearsPluginInstalled = null;
    public ?bool $silverlightPluginInstalled = null;
    public ?int $browserColorDept = null;
    public ?ScreenDimensions $webPageDimensions = null;
    public ?string $characterEncoding = null;
    public ?ScreenDimensions $browserViewportDimensions = null;
}
