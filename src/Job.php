<?php
namespace Hrgruri\Ricca;

class Job
{
    private $times      = null;
    private $messages   = null;
    private $now;

    public function __construct(\stdClass $val, array $now) {
        $this->now = $now;
        if (isset($val->time) && is_string($val->time)) {
            $this->times = explode(' ', $val->time);
        } else {
            $this->times = array();
        }
        if (isset($val->message) && is_string($val->message)) {
            $this->messages[] = $val->message;
        } elseif (isset($val->message) && is_array($val->message)) {
            $this->messages = $val->message;
        }
    }

    public function checkJob()
    {
        $flg = true;
        if (count($this->times) !== 5) {
            $flg = false;
        }
        foreach ($this->messages as $message) {
            if (!is_string($message)) {
                $flg = false;
                break;
            }
        }
        return $flg;
    }

    public function checkTime()
    {
        $flg = true;
        for ($i = 0; $i < 5; $i++) {
            $flg = $flg && ($this->times[$i] == '*' || $this->times[$i] == $this->now[$i]);
        }
        return $flg;
    }

    public function getMessage()
    {
        return $this->messages[array_rand($this->messages)];
    }
}
