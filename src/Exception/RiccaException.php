<?php
namespace Hrgruri\Ricca\Exception;

class RiccaException extends \Exception
{
    public $code;
    public $text;

    public function  __construct(string $code, string $text = null)
    {
        $this->code = $code;
        $this->text = $text ?? '';
    }
}
