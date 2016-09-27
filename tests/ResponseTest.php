<?php
use Hrgruri\Ricca\Response;

class ResponseTest extends TestCase
{
    public function testResponse()
    {
        $faker  = $this->faker();
        $text   = $faker->text();
        $data   = $faker->text();
        $res    = new Response(null, null);

        $new_res = $res->withText($text)->withData($data);
        $this->assertNull($res->getText());
        $this->assertNull($res->getData());
        $this->assertEquals($text, $new_res->getText());
        $this->assertEquals($data, $new_res->getData());
    }
}
