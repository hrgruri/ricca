<?php
namespace Hrgruri\Ricca\Command;

class Tweet extends \Hrgruri\Ricca\Command
{
    public function configure()
    {
        $this->setName('tw');
    }

    public function execute(\Hrgruri\Ricca\Request $req, \Hrgruri\Ricca\Response $res)
    {
        return $res->withTweet($req->getText())->withText('tweeted');
    }
}
