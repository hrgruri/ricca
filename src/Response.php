<?php
namespace Hrgruri\Ricca;

class Response
{
    private $text;
    private $data;
    private $tweet;

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

    public function getText()
    {
        return $this->text;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getTweet()
    {
        return $this->tweet;
    }
}
