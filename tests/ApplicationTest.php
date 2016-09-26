<?php
use Hrgruri\Ricca\Application;

class ApplicationTest extends TestCase
{
    public function testAdd()
    {
        $instance = new class extends \Hrgruri\Ricca\Command {
            public function configure()
            {
                $this->setName('foo');
            }

            public function execute($text){}
        };
        $app = $this->app();
        $this->assertTrue($app->add($instance));
        $this->assertFalse($app->add($instance));
    }

    public function testList()
    {
        $app  = $this->app();
        $list = $app->list();
        $this->assertInternalType('array', $list);
        $this->assertTrue(isset($list['pid']));
        $this->assertEquals('Hrgruri\Ricca\Command\Pid', $list['pid']);
    }

    public function testChannel()
    {
        $app = $this->app();
        $this->assertTrue($app->channel('pid', 'bot_channel'));
        $this->assertFalse($app->channel('undefined_command', 'bot_channel'));
    }
}
