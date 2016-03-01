<?php
namespace Hrgruri\Ricca\Exception;

class LiteException extends \Exception
{
    public function getDetail()
    {
        return "Lite Error: ". $this->getMessage();
    }
}
