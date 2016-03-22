<?php
namespace Hrgruri\Ricca;

use Hrgruri\Ricca\{KeyChain, Response};
use \Hrgruri\Ricca\Exception\{LiteException,CommandException};

class RiccaCommand
{
    private $keys;
    private $using;
    private $flag;
    private $dir;

    public function __construct($keys, $using)
    {
        $this->keys     =   $keys;
        $this->using    =   $using;
        $this->dir      =   dirname(__FILE__).'/data';
        $this->flag     =   true;
    }

    /**
    *   @param string $cmd: command
    *   @param string $opt: message
    *   @return null | string | int
    */
    public function fire(string $cmd, string $opt)
    {
        $result = null;
        if ($cmd !== '__construct' && $cmd !== 'fire' && method_exists($this,$cmd)) {
            $result = $this->$cmd();
        }else {
            $class = '\\Hrgruri\\Ricca\\Command\\'.ucfirst($cmd);
            if ($this->flag !== true) {
                throw new LiteException('Ricca is OFF');
            } elseif (!class_exists($class)) {
                throw new LiteException("UNDEFINED COMMAND: {$cmd}");
            }
            $key = \Hrgruri\Ricca\KeyChain::getKey($this->keys, $this->using, $cmd);
            $result = (new $class("{$cmd}.json"))->run($opt, $key);
        }
        return $result;
    }

    public function start()
    {
        $this->flag = true;
        return 'OK';
    }

    public function stop()
    {
        $this->flag = false;
        return 'OK';
    }

    private function exit()
    {
        exit();
    }
}
