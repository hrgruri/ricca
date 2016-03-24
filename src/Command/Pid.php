<?php
namespace Hrgruri\Ricca\Command;

class Pid extends \Hrgruri\Ricca\Command
{
    public function run($text, $key)
    {
        return (string)getmypid();
    }
}
