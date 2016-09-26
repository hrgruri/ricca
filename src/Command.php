<?php
namespace Hrgruri\Ricca;

abstract class Command
{
    private $name;
    private $channel;

    abstract public function configure();
    abstract public function execute($text);

    final public function __construct()
    {
        $this->configure();
    }

    /**
     * set Command name
     * @param string $name
     */
    final protected function setName(string $name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * get Command name
     * @return string
     */
    final public function getName() : string
    {
        return $this->name;
    }

    final public function setChannel(string $name)
    {
        $this->channel = $name;
        return $this;
    }

    final public function getChannel() : string
    {
        return $this->channel;
    }
}
