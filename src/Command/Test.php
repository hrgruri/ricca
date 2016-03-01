<?php
namespace Hrgruri\Ricca\Command;

use \Hrgruri\Ricca\Exception\CommandException;

class Test implements \Hrgruri\Ricca\CommandInterface
{
    public function run($opt, $key)
    {
        return new \Hrgruri\Ricca\Response(true, $opt);
    }
}
