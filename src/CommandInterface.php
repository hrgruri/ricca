<?php
namespace Hrgruri\Ricca;

interface CommandInterface
{
    public function run($opt, $key);
}
