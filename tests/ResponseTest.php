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
        $this->assertNull($res->text);
        $this->assertNull($res->data);
        $this->assertEquals($text, $new_res->text);
        $this->assertEquals($data, $new_res->data);
    }
}
