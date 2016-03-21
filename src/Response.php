<?php
namespace Hrgruri\Ricca;

class Response
{
    public $code;

    /**
    *   @param string $code: response code
    */
    public function __construct(string $code)
    {
        $this->code = $code;
    }
}
