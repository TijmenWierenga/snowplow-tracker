<?php

declare(strict_types=1);

namespace TijmenWierenga\SnowplowTracker\Config;

/**
 * @author Tijmen Wierenga <tijmen.wierenga@live.nl>
 */
enum Platform: string
{
    case WEB = 'web';
    case MOBILE = 'mob';
    case PC = 'pc';
    case SERVER_SIDE_APP = 'srv';
    case GENERAL_APP = 'app';
    case CONNECTED_TV = 'tv';
    case GAMES_CONSOLE = 'cnsl';
    case INTERNET_OF_THINGS = 'iot';
}
