<?php
namespace Hrgruri\Ricca\Command;

use \Hrgruri\Ricca\Exception\CommandException;

class Pid extends \Hrgruri\Ricca\Command
{
    public function run($opt, $key)
    {
        return (string)getmypid();
    }
}
