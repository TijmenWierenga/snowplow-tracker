[![Latest Stable Version](http://poser.pugx.org/tijmenwierenga/snowplow-tracker/v)](https://packagist.org/packages/tijmenwierenga/snowplow-tracker) [![Total Downloads](http://poser.pugx.org/tijmenwierenga/snowplow-tracker/downloads)](https://packagist.org/packages/tijmenwierenga/snowplow-tracker) [![Latest Unstable Version](http://poser.pugx.org/tijmenwierenga/snowplow-tracker/v/unstable)](https://packagist.org/packages/tijmenwierenga/snowplow-tracker) [![License](http://poser.pugx.org/tijmenwierenga/snowplow-tracker/license)](https://packagist.org/packages/tijmenwierenga/snowplow-tracker) [![PHP Version Require](http://poser.pugx.org/tijmenwierenga/snowplow-tracker/require/php)](https://packagist.org/packages/tijmenwierenga/snowplow-tracker)

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

### HttpClientEmitter
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

### Tracker configuration
In order to customize the tracker's configuration you can pass an additional configuration object:

```php
<?php

declare(strict_types=1);

use TijmenWierenga\SnowplowTracker\Tracker;
use TijmenWierenga\SnowplowTracker\Config\Platform;
use TijmenWierenga\SnowplowTracker\Config\TrackerConfig;

$config = new TrackerConfig(
    Platform::WEB, // The platform you're sending events from
    'My Tracker Name', // The name of your tracker
    'my-app-id' // A unique identifier for your app
);
$tracker = new Tracker(config: $config);
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

### The Snowplow Tracker protocol
All events extend from a base event class which implements all properties currently available in the [Snowplow Tracker Protocol](https://docs.snowplowanalytics.com/docs/collecting-data/collecting-from-own-applications/snowplow-tracker-protocol/).
These properties are publicly available in every event.
The example below shows how to add a `userId` to an event to identify a user:

```php
<?php

declare(strict_types=1);

use TijmenWierenga\SnowplowTracker\Events\Pageview;

$pageviewEvent = new Pageview('https://github.com/TijmenWierenga');

$pageviewEvent->userId = 'TijmenWierenga';
```

### Custom context
Sometimes you want to add additional context to an event.
Custom contexts are self-describing JSON schema's which can be implemented by creating a class that implements `TijmenWierenga\SnowplowTracker\Events\Schemable`.
The example below shows an implementation of the existing [Timing JSON Schema](https://github.com/snowplow/iglu-central/blob/master/schemas/com.snowplowanalytics.snowplow/timing/jsonschema/1-0-0) as defined by Snowplow Analytics.

```php
<?php

declare(strict_types=1);

use TijmenWierenga\SnowplowTracker\Events\Schemable;
use TijmenWierenga\SnowplowTracker\Schemas\Schema;
use TijmenWierenga\SnowplowTracker\Schemas\Version;

final class Timing implements Schemable
{
    public function __construct(
        private readonly string $category,
        private readonly string $variable,
        private readonly int $timing,
        private readonly ?string $label = null
    ) {
    }
    
    public function getSchema(): Schema
    {
        return new Schema(
            'com.snowplowanalytics.snowplow',
            'timing',
            new Version(1, 0, 0)
        );
    }
    
    public function getData(): array|string|int|float|bool|JsonSerializable
    {
        return array_filter([
            'category' => $this->category,
            'variable' => $this->variable,
            'timing' => $this->timing,
            'label' => $this->label
        ]);
    } 
}
```

As an example, let's include context about the page load in a pageview event:
```php
<?php

declare(strict_types=1);

use TijmenWierenga\SnowplowTracker\Events\Pageview;

$pageviewEvent = new Pageview('https://github.com/TijmenWierenga');

$pageLoad = new Timing(
    'pageLoad',
    'ms',
    21
);

$pageviewEvent->addContext($pageLoad);
```

### Middleware
Middlewares provides a way to act on events that are tracked.
Every piece of middleware is a callable that receives the event as an argument and must return the (modified) event to the next piece of middleware:

```php
callable(Event $event): Event
```

This is incredibly useful when you want to add contextual information to every event.
As an example, middleware is added that adds the `userId` of the currently authenticated user to the event.

```php
<?php

declare(strict_types=1);

use TijmenWierenga\SnowplowTracker\Events\Event;
use TijmenWierenga\SnowplowTracker\Events\Pageview;
use TijmenWierenga\SnowplowTracker\Events\StructuredEvent;
use TijmenWierenga\SnowplowTracker\Tracker;

final class AddUserIdMiddleware
{
    public function __construct(
        private readonly AuthenticatedUserIdProvider $userIdProvider
    ) {
    }
    
    public function __invoke(Event $event): Event
    {
        $event->userId = $this->userIdProvider->getUserId();
        
        return $event;
    }
}

$addUserIdMiddleware = new AddUserIdMiddleware(/** ... */);

$tracker = new Tracker(middleware: [$addUserIdMiddleware]);

$pageviewEvent = new Pageview('https://github.com/TijmenWierenga');
$structuredEvent = new StructuredEvent('my-category', 'my-action');

$tracker->track($pageviewEvent);
$tracker->track($structuredEvent);
```

In the example above both events will now have a `userId` attached.
