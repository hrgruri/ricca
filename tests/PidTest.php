<?php
class PidTest extends TestCase
{
    public function testExecute()
    {
        $instance = new \Hrgruri\Ricca\Command\Pid();
        $result = $instance->execute($this->path);
        $this->assertInternalType('string', $result);
        $this->assertTrue(is_numeric($result));
    }
}
