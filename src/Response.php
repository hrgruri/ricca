<?php
namespace Hrgruri\Ricca;

class Response
{
    private $text;
    private $data;
    private $tweet;
    private $clear;

    public function __get($name)
    {
        return $this->{$name} ?? null;
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

    public function withTweet(string $text)
    {
        $clone = clone $this;
        $clone->tweet = $text;
        return $clone;
    }

    public function withClear()
    {
        $clone = clone $this;
        $clone->clear = true;
        return $clone;
    }
}
