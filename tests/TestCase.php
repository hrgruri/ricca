<?php
use Hrgruri\Ricca\Application;
use org\bovigo\vfs\{
    vfsStream,
    vfsStreamWrapper,
    vfsStreamDirectory
};

class TestCase extends \PHPUnit\Framework\TestCase
{
    protected $path = __DIR__ . '/../data';
    protected $root;

    public function setUp()
    {
        vfsStreamWrapper::register();
        vfsStreamWrapper::setRoot(new vfsStreamDirectory('ricca'));
        $this->root = vfsStream::url('ricca');
        if (!file_exists($this->file('key.json'))) {
            file_put_contents($this->file('key.json'), json_encode(["slack" => ""]));
        }
    }

    /**
     * @param  string $name file name
     * @return string
     */
    protected function file(string $name) : string
    {
        $name = ltrim($name, '/');
        return "{$this->root}/$name";
    }

    /**
     * @param  mixed    $instance
     * @param  string   $name method name
     * @param  array    $args arguments
     * @return mixed
     */
    protected function callMethod($instance, string $name, array $args = [])
    {
        $method = new  ReflectionMethod($instance, $name);
        $method->setAccessible(true);
        return $method->invokeArgs($instance, $args);
    }

    /**
     * get faker
     * @return FakerGenerator
     */
    protected function faker() : \Faker\Generator
    {
        return \Faker\Factory::create();
    }

    protected function app() : Application
    {
        return  new Application($this->root);
    }
}
