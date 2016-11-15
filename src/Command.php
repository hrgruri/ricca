<?php
namespace Hrgruri\Ricca;

abstract class Command
{
    private $name;

    abstract public function configure();

    /**
     * @param  Hrgruri\Ricca\Request    $request
     * @param  Hrgruri\Ricca\Response   $response
     * @return Hrgruri\Ricca\Response | string
     */
    abstract public function execute(
        \Hrgruri\Ricca\Request $request,
        \Hrgruri\Ricca\Response $response
    );

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
}
