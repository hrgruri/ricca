<?php
namespace Hrgruri\Ricca\Exception;

class CommandException extends \Exception
{
    public function getDetail()
    {
        $res = "ERROR: ". $this->getMessage().PHP_EOL;
        // $res.= "FILE: ". $this->getFile().PHP_EOL;
        // $res.= "LINE: ". $this->getLine().PHP_EOL;
        return $res;
    }
}
