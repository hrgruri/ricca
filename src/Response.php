<?php
namespace Hrgruri\Ricca;

class Response
{
    //  Response Type
    const MESSAGE   = 1;
    const CODE      = 2;

    public $text;
    public $type;
    public $flag;

    /**
     * @param $text response [message | code]
     * @param $type response type
     * @param $flag flag of interactive mode
     */
    public function __construct(string $text = null, int $type = null, bool $flag = null)
    {
        $this->text = $text;
        $this->type = $type ?? self::MESSAGE;
        $this->flag = $flag ?? false;
    }

    public function interactive(bool $flag = null)
    {
        $this->interactive_flag = $flag ?? true;
        return $this;
    }

    public function message(string $text, bool $flag = null)
    {
        $this->text =   $text;
        $this->type =   self::MESSAGE;
        $this->flag =   $flag ?? false;
        return $this;
    }

    public function code(string $code, bool $flag = null)
    {
        $this->text = $code;
        $this->type = self::CODE;
        $this->flag = $flag ?? false;
        return $this;
    }
}
