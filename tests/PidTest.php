<?php
class PidTest extends TestCase
{
    public function testExecute()
    {
        $instance = new \Hrgruri\Ricca\Command\Pid();
        $result = $instance->execute(new \Hrgruri\Ricca\Request('', null));
        $this->assertInternalType('string', $result);
        $this->assertTrue(is_numeric($result));
    }
}
