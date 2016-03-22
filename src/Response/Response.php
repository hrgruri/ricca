<?php
namespace Hrgruri\Ricca\Response;

class Response
{
    public $code;
    public $interactive_flag;

    /**
     * @param $code response code
     * @param $flag interactive flag
     */
    public function __construct(string $code, bool $flag = null)
    {
        $this->code =   $code;
        $this->interactive_flag =   isset($flag) ?? false;
    }
}
