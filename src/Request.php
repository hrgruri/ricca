<?php
namespace Hrgruri\Ricca;

class Request
{
    private $text;
    private $data;

    public function __construct(string $text, $data = null)
    {
        $this->text = $text;
        $this->data = $data;
    }

    public function __get(string $name)
    {
        return $this->{$name} ?? null;
    }

    public function getText() : string
    {
        return $this->text;
    }

    public function getData()
    {
        return $this->data;
    }
}
