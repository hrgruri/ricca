# Ricca is Slack Bot
## Overview
If you send a specific message, Ricca(Bot) will do action instead of you.
## Setting
### Config
config/allow.jsno
```json
[
    "USER_NAME"
]
```
config/keys.json
```json
{
    "slack":"TOKEN",
    "twitter":{
        "consumer_key"      :"CONSUMER_KEY",
        "consumer_secret"   :"CONSUMER_SECRET",
        "oauth_token"       :"OAUTH_TOKEN",
        "oauth_token_secret":"OAUTH_TOKEN_SECRET"
    }
}
```

config/bot.json
```json
[
    {
        "id": "1",
        "pattern" :"/^hi$/",
        "response":[
            "Hi!", ":)"
        ]
    }
]
```
## Usage
```php
<?php
// require vendor/autoload.php
require (dirname(__FILE__).'/../vendor/autoload.php');
// set config directory
$dir    =   dirname(__FILE__).'/config';

$ricca = new \Hrgruri\Ricca\Ricca($dir);
$ricca->run();
```
