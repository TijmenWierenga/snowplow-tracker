# Snowplow Tracker
An alternative to the original Snowplow Tracker.

This Tracker provides:
* an object-oriented API for event tracking
* extension points in order to integrate your own domain logic
* abstractions to swap dependencies

## Installation
With [composer](https://getcomposer.org/):

```sh
composer require tijmenwierenga/snowplow-tracker
```

## Setup
The Snowplow Tracker is instantiable by providing an emitter and optionally additional configuration.
Currently, only a single emitter is available: the `HttpClientEmitter`.

The `HttpClientEmitter` sends the payload to a collector over HTTP.
If you want to use this emitter a [PSR-7](https://www.php-fig.org/psr/psr-7/), [PSR-17](https://www.php-fig.org/psr/psr-17/) and [PSR-18](https://www.php-fig.org/psr/psr-18/) compliant implementation needs to be installed.

Popular PSR-18 compliant HTTP Clients are:
* [symfony/http-client](https://symfony.com/doc/current/http_client.html)
* [guzzlehttp/guzzle](https://docs.guzzlephp.org/en/stable/)

Popular PSR-7 compliant libraries are:
* [guzzlehttp/psr7](https://github.com/guzzle/psr7)
* [nyholm/psr7](https://github.com/Nyholm/psr7)

By default, the `php-http/discovery` will discover the installed HTTP Client and Request Factory so no additional configuration is required.
If you wish to configure your HTTP client yourself you can pass in your own. Same goes for the Request Factory.

With auto-discovery:
```php
<?php

declare(strict_types=1);

use TijmenWierenga\SnowplowTracker\Tracker;
use TijmenWierenga\SnowplowTracker\Emitters\HttpClientEmitter;

$emitter = new HttpClientEmitter('https://your-collector-uri')
$tracker = new Tracker($emitter)
```

Without auto-discovery (with Symfony's HTTP client):
```php
<?php

declare(strict_types=1);

use Symfony\Component\HttpClient\Psr18Client;
use TijmenWierenga\SnowplowTracker\Emitters\HttpClientEmitter;
use TijmenWierenga\SnowplowTracker\Tracker;

// Instantiate your own HTTP Client
$httpClient = new Psr18Client();

// Pass it to the emitter
$emitter = new HttpClientEmitter('https://your-collector-uri', $httpClient);
$tracker = new Tracker($emitter);
```

## Usage
Tracking events is done by calling the `track()` method on the `Tracker` instance:
This library implements 6 type of events.

### Pageviews

```php
<?php

declare(strict_types=1);

use TijmenWierenga\SnowplowTracker\Events\Pageview;

$tracker->track(new Pageview(
    'https://github.com/TijmenWierenga',
    'Tijmen Wierenga (Tijmen Wierenga)',
    'https://twitter.com/TijmenWierenga'
));
```

### Page pings

```php
<?php

declare(strict_types=1);

use TijmenWierenga\SnowplowTracker\Events\PagePing;

$tracker->track(new PagePing(
    0, // min horizontal offset
    500, // max horizontal offset
    250, // min vertical offset
    300 // max vertical offset
));
```

### Ecommerce transactions

```php
<?php

declare(strict_types=1);

use TijmenWierenga\SnowplowTracker\Events\EcommerceTransaction;

$tracker->track(new EcommerceTransaction(
    'd85e7b63-c046-47ac-b9a9-039d33ef3b3b', // order ID
    49.95, // total value
));
```

### Transaction items

```php
<?php

declare(strict_types=1);

use TijmenWierenga\SnowplowTracker\Events\TransactionItem;

$tracker->track(new TransactionItem(
    'd85e7b63-c046-47ac-b9a9-039d33ef3b3b', // order ID
    '48743-48284-24', // SKU
    12.95, // price
    4 // quantity
));
```

### Structured events

```php
<?php

declare(strict_types=1);

use TijmenWierenga\SnowplowTracker\Events\StructuredEvent;

$tracker->track(new StructuredEvent(
    'my-category',
    'my-action'
));
```

### Unstructured events

```php
<?php

declare(strict_types=1);

use TijmenWierenga\SnowplowTracker\Events\UnstructuredEvent;
use TijmenWierenga\SnowplowTracker\Schemas\Schema;
use TijmenWierenga\SnowplowTracker\Schemas\Version;

$tracker->track(new UnstructuredEvent(
    new Schema(
        'com.snowplowanalytics.snowplow',
        'ad_impression',
        new Version(1, 0, 0)
    ),
    [
        'impressionId' => 'dcefa2cc-9e82-4d7e-bbeb-eef0e9dad57d'
    ]
));
```
