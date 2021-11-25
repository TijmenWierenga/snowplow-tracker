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
The Snowplow Tracker is instantiable by providing an emitter and optional additional configuration:

```php
<?php

declare(strict_types=1);

use Symfony\Component\HttpClient\HttpClient;
use TijmenWierenga\SnowplowTracker\{
    Emitters\HttpClientEmitter,
    Tracker
};

$httpClient = HttpClient::createForBaseUri('https://your-collector-uri');
$emitter = new HttpClientEmitter($httpClient)
$tracker = new Tracker($emitter)
```

## Usage
Tracking events is done by calling the `track()` method on the `Tracker` instance:

```php
<?php

declare(strict_types=1);

use TijmenWierenga\SnowplowTracker\Events\Pageview;

// Instantiate a pageview event
$event = new Pageview(
    'https://github.com/TijmenWierenga',
    'Tijmen Wierenga (Tijmen Wierenga)',
    'https://twitter.com/TijmenWierenga'
);

// Track the event
$tracker->track($event);
```
