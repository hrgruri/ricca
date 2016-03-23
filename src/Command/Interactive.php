<?php
namespace Hrgruri\Ricca\Command;

use \Hrgruri\Ricca\Exception\CommandException;
use \Hrgruri\Ricca\Response;

class Interactive extends \Hrgruri\Ricca\Command
{
    public function run($opt, $key)
    {
        return (new Response($opt, Response::MESSAGE, true));
    }
}
