<?php
namespace Hrgruri\Ricca\Command;

use \Hrgruri\Ricca\CommandException;

class Tw implements \Hrgruri\Ricca\CommandInterface
{
    public function run($opt, $key)
    {
        $result = null;
        if ( isset($key)) {
            $twitter = new \Hrgruri\Ricca\TwitterAPI($key);
            $twitter->post($opt);
            $result = new \Hrgruri\Ricca\Response(true);
        }else {
            $result = new \Hrgruri\Ricca\Response(false);
        }
        return $result;
    }
}
