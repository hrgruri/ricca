<?php
namespace Hrgruri\Ricca\Command;

use \Hrgruri\Ricca\Exception\CommandException;

class Test extends \Hrgruri\Ricca\Command
{
    public function run($text, $key)
    {
        return new \Hrgruri\Ricca\Response($text);
    }
}
