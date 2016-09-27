<?php
namespace Hrgruri\Ricca;

class Response
{
    private $text;
    private $data;

    public function __construct(string $text = null, $data = null)
    {
        $this->text = $text;
        $this->data = $data;
    }

    public function withText(string $text)
    {
        $clone = clone $this;
        $clone->text = $text;
        return $clone;
    }

    public function withData($data)
    {
        $clone = clone $this;
        $clone->data = $data;
        return $clone;
    }

    public function getText()
    {
        return $this->text;
    }

    public function getData()
    {
        return $this->data;
    }
}
