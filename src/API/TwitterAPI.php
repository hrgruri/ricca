<?php
namespace Hrgruri\Ricca\API;

use Abraham\TwitterOAuth\TwitterOAuth;

class TwitterAPI
{
    private $connection;

    public function __construct($key)
    {
        $this->connection = new TwitterOAuth(
            $key->consumer_key,
            $key->consumer_secret,
            $key->oauth_token,
            $key->oauth_token_secret
        );
    }

    public function post(string $msg)
    {
        $res = false;
        $msg = mb_convert_kana($msg, 'as');
        $len = mb_strlen($msg);
        if ($len > 0 && $len <= 140) {
            $res = $this->connection->post("statuses/update", array("status" => $msg));
            $res = true;
        }
        return $res;
    }
}
