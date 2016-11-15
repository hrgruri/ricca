<?php
use Hrgruri\Ricca\Command\Pid;

class CommandTest extends TestCase
{
    public function testName()
    {
        $instance = new Pid();
        $faker    = $this->faker();
        $word     = $faker->word;
        $instance = $this->callMethod($instance, 'setName', [$word]);
        $this->assertInstanceOf(Pid::class, $instance);
        $result   = $this->callMethod($instance, 'getName');
        $this->assertEquals($word, $result);
    }
}
