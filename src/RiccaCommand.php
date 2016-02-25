<?php
namespace Hrgruri\Ricca;

use \Hrgruri\Ricca\{SlackAPI, TwitterAPI};

class RiccaCommand
{
    private $key;
    private $twitter;

    public function __construct($key)
    {
        $this->key = $key;
        $this->twitter = new \Hrgruri\Ricca\TwitterAPI($key->twitter);
    }

    public function tw(string $opt)
    {
        $res = $this->twitter->post($opt);
        return $res;
    }
}
