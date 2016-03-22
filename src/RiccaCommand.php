<?php
namespace Hrgruri\Ricca;

use \Hrgruri\Ricca\{SlackAPI, TwitterAPI};
use \Hrgruri\Ricca\Exception\{LiteException,CommandException};

class RiccaCommand
{
    private $keys;
    private $using;
    private $flg;
    private $dir;

    public function __construct($keys, $using)
    {
        $this->keys     =   $keys;
        $this->using    =   $using;
        $this->dir      =   dirname(__FILE__).'/data';
        $this->flg      =   true;
    }

    /**
    *   @param string $cmd: command
    *   @param string $opt: message
    *   @return null | string | int
    */
    public function fire(string $cmd, string $opt)
    {
        $result = null;
        $cmd    = mb_strtolower($cmd);
        if ($cmd !== '__construct' && $cmd !== 'fire' && method_exists($this,$cmd)) {
            $result = $this->$cmd();
        } else {
            $class = '\\Hrgruri\\Ricca\\Command\\'.ucfirst($cmd);
            if ($this->flg !== true) {
                throw new LiteException('Ricca is OFF');
            } elseif (!class_exists($class)) {
                throw new LiteException("UNDEFINED COMMAND: {$cmd}");
            }
            if (property_exists($this->using, $cmd)) {
                $key = property_exists($this->keys, $this->using->$cmd) ? $this->keys->{$this->using->$cmd} : null;
            } else {
                $key = null;
            }
            $result = (new $class("{$cmd}.json"))->run($opt, $key);
        }
        return $result;
    }

    public function start()
    {
        $this->flg = true;
        return 'OK';
    }

    public function stop()
    {
        $this->flg = false;
        return 'OK';
    }
}
