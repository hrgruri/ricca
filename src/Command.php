<?php
namespace Hrgruri\Ricca;

use Hrgruri\Ricca\Request;
use Hrgruri\Ricca\Response;

abstract class Command
{
    private $name;
    private $channel;

    abstract public function configure();
    
    /**
     * @param  Hrgruri\Ricca\Request    $request
     * @param  Hrgruri\Ricca\Response   $response
     * @return Hrgruri\Ricca\Response | string
     */
    abstract public function execute(Request $request, Response $response);

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
