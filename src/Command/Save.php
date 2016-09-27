<?php
namespace Hrgruri\Ricca\Command;

class Save extends \Hrgruri\Ricca\Command
{
    public function configure()
    {
        $this->setName('save')
            ->setChannel('general');
    }

    public function execute(\Hrgruri\Ricca\Request $req, \Hrgruri\Ricca\Response $res)
    {
        $data   = $req->getData();
        $data[] = $req->getText();
        return $res->withText('saved')->withData($data);
    }
}
