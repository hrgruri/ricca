<?php
namespace Hrgruri\Ricca\Command;

use \Hrgruri\Ricca\Exception\CommandException;

class Interactive extends \Hrgruri\Ricca\Command
{
    public function run($opt, $key)
    {
        return (new \Hrgruri\Ricca\Response\ResponsePlus)->message($opt, true);
    }
}
