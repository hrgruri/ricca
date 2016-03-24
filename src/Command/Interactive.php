<?php
namespace Hrgruri\Ricca\Command;

use \Hrgruri\Ricca\Response;

class Interactive extends \Hrgruri\Ricca\Command
{
    public function run($text, $key)
    {
        return (new Response($text, Response::MESSAGE, true));
    }
}
