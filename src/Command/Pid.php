<?php
namespace Hrgruri\Ricca\Command;

class Pid extends \Hrgruri\Ricca\Command
{
    public function configure()
    {
        $this->setName('pid');
    }

    public function execute(\Hrgruri\Ricca\Request $req, \Hrgruri\Ricca\Response $res)
    {
        return (string)getmypid();
    }
}
