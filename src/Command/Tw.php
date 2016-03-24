<?php
namespace Hrgruri\Ricca\Command;

use Abraham\TwitterOAuth\TwitterOAuth;

class Tw extends \Hrgruri\Ricca\Command
{
    public function run($text, $key)
    {
        $result = null;
        $text = mb_convert_kana($text, 'as');
        $len = mb_strlen($text);
        if ($len > 0 && $len <= 140 && isset($key['twitter'])) {
            $connection = new TwitterOAuth(
                $key['twitter']->consumer_key       ?? '',
                $key['twitter']->consumer_secret    ?? '',
                $key['twitter']->oauth_token        ?? '',
                $key['twitter']->oauth_token_secret ?? ''
            );
            $tmp = $connection->post("statuses/update", array("status" => $text));
            if (isset($tmp->errors)) {
                $result = (new \Hrgruri\Ricca\Response)->code('untweet');
            } else {
                $result = (new \Hrgruri\Ricca\Response)->code('tweet');
            }
        } else {
            $result = (new \Hrgruri\Ricca\Response)->code('untweet');
        }
        return $result;
    }
}
