<?php
use Hrgruri\Ricca\Application;

class TestCase extends \PHPUnit\Framework\TestCase
{
    protected $path = __DIR__ . '/../data';

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
        return new Application($this->path);
    }
}
