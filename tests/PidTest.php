<?php
use Hrgruri\Ricca\Request;
use Hrgruri\Ricca\Response;

class PidTest extends TestCase
{
    public function testExecute()
    {
        $instance = new \Hrgruri\Ricca\Command\Pid();
        $result = $instance->execute(new Request('', null), new Response);
        $this->assertInternalType('string', $result);
        $this->assertTrue(is_numeric($result));
    }
}
