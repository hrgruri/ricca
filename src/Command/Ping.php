<?php
namespace Hrgruri\Ricca\Command;

use \Hrgruri\Ricca\Exception\CommandException;

class Ping extends \Hrgruri\Ricca\Command
{
    public function run($opt, $key)
    {
        return "ok";
    }
}
