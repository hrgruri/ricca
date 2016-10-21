<?php
use Hrgruri\Ricca\Request;

class RequestTest extends TestCase
{
    public function testGetMethods()
    {
        $faker  = $this->faker();
        $text   = $faker->text();
        $data   = $faker->text();
        $req    = new Request($text, $data);

        $this->assertEquals($text, $req->getText());
        $this->assertEquals($text, $req->text);

        $this->assertEquals($data, $req->getData());
        $this->assertEquals($data, $req->data);
    }
}
