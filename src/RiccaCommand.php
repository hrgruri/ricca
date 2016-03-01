<?php
namespace Hrgruri\Ricca;

use \Hrgruri\Ricca\{SlackAPI, TwitterAPI};
use \Hrgruri\Ricca\Exception\{LiteException,CommandException};

class RiccaCommand
{
    private $keys;
    private $aliases;
    private $flg;

    public function __construct($keys, $aliases)
    {
        $this->keys     = $keys;
        $this->aliases  = $aliases;
        $this->flg      = true;
    }

    public function fire(string $cmd, string $opt)
    {
        $result = null;
        $cmd = lcfirst($cmd);
        if ($cmd === 'start' || $cmd === 'stop') {
            $this->$cmd();
            $result = new \Hrgruri\Ricca\Response(true, 'OK.');
        } else {
            $class = '\\Hrgruri\\Ricca\\Command\\'.ucfirst($cmd);
            if ($this->flg !== true) {
                throw new LiteException('Ricca is OFF');
            } elseif (!class_exists($class)) {
                throw new CommandException("UNDEFINED COMMAND: {$cmd}");
            }
            if (property_exists($this->aliases, $cmd)) {
                $key = property_exists($this->keys, $this->aliases->$cmd) ? $this->keys->{$this->aliases->$cmd} : null;
            } else {
                $key = null;
            }
            $result = (new $class)->run($opt, $key);
        }
        return $result;
    }

    public function start()
    {
        $this->flg = true;
    }

    public function stop()
    {
        $this->flg = false;
    }
}
