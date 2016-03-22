<?php
namespace Hrgruri\Ricca\Command;

use \Hrgruri\Ricca\Exception\CommandException;

class Tw extends \Hrgruri\Ricca\Command
{
    public function run($opt, $key)
    {
        $result = null;
        if (isset($key)) {
            $twitter = new \Hrgruri\Ricca\API\TwitterAPI($key['twitter']);
            $twitter->post($opt);
            $result = new \Hrgruri\Ricca\Response\Response('tweet');
        }
        return $result;
    }
}
