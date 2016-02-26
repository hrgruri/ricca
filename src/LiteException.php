<?php
namespace Hrgruri\Ricca;

class LiteException extends \Exception
{
    public function getDetail()
    {
        return "Lite Error: ". $this->getMessage();
    }
}
