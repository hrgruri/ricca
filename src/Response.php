<?php
namespace Hrgruri\Ricca;

class Response
{
    public $flg;
    public $msg;
    public $code;

    public function __construct(bool $flg, string $msg = null, int $code = null)
    {
        $this->flg = $flg;
        if (is_string($msg)) {
            $this->msg = $msg;
        } else {
            $this->msg = null;
        }
        $this->code = $code ?? 0;
    }
}
