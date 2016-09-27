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
    "slack" : "TOKEN"
}
```
data/user.json
```json
{
    "admin" : ["USERNAME"]
}
```

## Usage
```php
php bot.php
```
