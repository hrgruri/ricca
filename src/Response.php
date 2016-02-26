<?php
namespace Hrgruri\Ricca;

class Response
{
    public $flg;
    public $msg;

    public function __construct(bool $flg, $msg = null)
    {
        $this->flg = $flg;
        if (is_string($msg)) {
            $this->msg = $msg;
        } else {
            $this->msg = null;
        }
    }
}
