<?php
namespace Hrgruri\Ricca\Exception;

class RiccaException extends \Exception
{
    public $code;

    public function  __construct(string $code)
    {
        $this->code = $code;
    }
}
