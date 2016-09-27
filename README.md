# Ricca is Slack Bot

## Installation

```sh
composer require hrgruri/ricca
```

### bot.php
```php
<?php
require 'vendor/autoload.php';

$app = new Hrgruri\Ricca\Application(__DIR__.'/data', 'general');
// $app->add(new Hrgruri\Ricca\Command\Save());
$app->run();
```

### Setting
data/key.json
```json
{
    "slack"     : "TOKEN",
    "twitter"   : {
        "consumer_key"       : "CONSUMER_KEY",
        "consumer_secret"    : "CONSUMER_SECRET",
        "oauth_token"        : "OAUTH_TOKEN",
        "oauth_token_secret" : "OAUTH_TOKEN_SECRET"
    }
}

```
data/user.json
```json
{
    "admin" : ["USERNAME"],
    "allow" : []
}
```

## Usage
```php
php bot.php
```
