<?php
namespace Hrgruri\Ricca\Command;

class Pid extends \Hrgruri\Ricca\Command
{
    public function configure()
    {
        $this->setName('pid')
            ->setChannel('general');
    }

    public function execute(\Hrgruri\Ricca\Request $req)
    {
        return (string)getmypid();
    }
}
