<?php
namespace Hrgruri\Ricca;

class CommandException extends \Exception
{
    public function getDetail()
    {
        $res = "ERROR\n";
        $res.= "NOTE: ". $this->getMessage().PHP_EOL;
        // $res.= "FILE: ". $this->getFile().PHP_EOL;
        // $res.= "LINE: ". $this->getLine().PHP_EOL;
        return $res;
    }
}
