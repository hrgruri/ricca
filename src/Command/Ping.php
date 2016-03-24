<?php
namespace Hrgruri\Ricca\Command;

class Ping extends \Hrgruri\Ricca\Command
{
    public function run($text, $key)
    {
        return "ok";
    }
}
