<?php
namespace Hrgruri\Ricca\Response;

class ResponsePlus
{
    /**
     * @var string $text                response [code | message]
     * @var bool $flag                   response code flag
     * @var bool $interactive_flag      interactive_flag
     */
    public $text    =   null;
    public $flag    =   true;
    public $interactive_flag = false;

    /**
     * @param $text response [code | text] ($flag: true | false)
     * @param $flag response code flag
     * @param $interactive_flag
     */
    public function __construct(string $text = null, bool $flag = null, bool $interactive_flag = null)
    {
        $this->text = isset($text) ? $text : $this->text;
        $this->flag = isset($flag) ? $flag : $this->flag;
        $this->interactive_flag = isset($interactive_flag) ? $interactive_flag : $this->interactive_flag;
    }

    public function interactive(bool $interactive_flag = null)
    {
        $this->interactive_flag = $interactive_flag ?? true;
        return $this;
    }

    public function code(string $code, bool $interactive_flag = null)
    {
        $this->text = $code;
        $this->flag  = true;
        $this->interactive_flag = $interactive_flag ?? false;
        return $this;
    }

    public function message(string $text, bool $interactive_flag = null)
    {
        $this->text =   $text;
        $this->flag =   false;
        $this->interactive_flag = $interactive_flag ?? false;
        return $this;
    }
}
