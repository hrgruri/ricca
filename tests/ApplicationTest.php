<?php
use Hrgruri\Ricca\{
    Application,
    Request,
    Response
};

class ApplicationTest extends TestCase
{
    public function testAdd()
    {
        $instance = new class extends \Hrgruri\Ricca\Command {
            public function configure()
            {
                $this->setName('foo');
            }

            public function execute(Request $req, Response $res){}
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

    public function testChannelAll()
    {
        $app = $this->app();
        $this->assertTrue($app->channelAll('bot_channel'));
    }

    public function testSave()
    {
        $app    = $this->app();
        $faker  = $this->faker();
        $name   = $faker->word;
        $text   = $faker->text();
        $this->callMethod($app, 'save', [$name, $text]);
        $this->assertTrue(file_exists($this->file("storage/{$name}.json")));
        $data = $this->callMethod($app, 'read', [$name]);
        $this->assertEquals($text, $data);
    }

    public function testProcessResponse()
    {
        $app     = $this->app();
        $faker   = $this->faker();
        $text    = $faker->text();
        $command = new Hrgruri\Ricca\Command\Pid();
        $res     = (new \Hrgruri\Ricca\Response)->withData($text);
        $this->callMethod($app, 'processResponse', [$command, $res]);
        $data = $this->callMethod($app, 'read', [$command->getName()]);
        $this->assertEquals($text, $data);
    }
}
