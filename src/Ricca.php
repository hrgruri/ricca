<?php
namespace Hrgruri\Ricca;

use \Hrgruri\Ricca\{SlackAPI, RiccaCommand};

class Ricca
{
    private $slack;
    private $keys;
    private $root;
    private $allow;
    private $rc;
    private $botName;

    public function __construct(string $dir)
    {
        $this->root     =   $dir;
        $this->keys      =   json_decode(file_get_contents("{$this->root}/keys.json"));
        $this->slack    =   new \Hrgruri\Ricca\SlackAPI($this->keys->slack);
        $this->allow    =   json_decode(file_get_contents("{$this->root}/allow.json"));
        $this->rc       =   new RiccaCommand($this->keys);
        $this->botName  =   $this->slack->getTokenUser();
    }

    public function run()
    {
        $loop  = \React\EventLoop\Factory::create();
        $client = new \Slack\RealTimeClient($loop);
        $client->setToken($this->keys->slack);
        $client->on('message', function ($data) use ($client) {
            $this->fire($data);
            // $client->disconnect();
        });

        $client->connect()->then(function () {
            print "Connected".PHP_EOL;
        });

        $loop->run();
    }

    private function fire($data)
    {
        $res = false;
        if ($this->botName === $data['user']) {
            return true;
        }
        try {
            $user       =   $this->slack->getUserById($data['user']);
            $channel    =   $this->slack->getChannelById($data['channel']);
            $text       =   mb_convert_kana($data['text'], 'as');
            if (!$this->isAllowUser($user)) {
                throw new \Exception("{$user} is deny user", 1);
            }
            // ALL CHANNELS
            if (preg_match('/^(\S*)(\s.*|)/', $text, $matched) === 1) {
                $cmd = lcfirst($matched[1]);
                if($cmd === '__construct') {
                    throw new \Exception("cannot use this command (__construct)", 1);
                }
                if (is_callable(array($this->rc, $cmd))) {
                    $res = $this->rc->$cmd(trim($matched[2]));
                } else {
                    throw new \Exception("undefined command", 1);
                }
            } else {
                throw new \Exception("Not matched", 1);
            }
            if ($res === true) {
                $this->response($cmd, $channel);
            }
        } catch (\Exception $e) {
            // print $e->getMessage().PHP_EOL;
            $res = false;
        }
        return $res;
    }

    private function isAllowUser($name)
    {
        return in_array($name, $this->allow->slack);
    }

    private function response(string $cmd, string $channel)
    {
        $data = json_decode(file_get_contents("{$this->root}/response.json"));
        if (isset($data->$cmd)) {
            $text = $data->$cmd[array_rand($data->$cmd)];
            $this->slack->postMsg($text, $channel);
        } else {
            $this->slack->postMsg('undefined response', $channel);
        }
    }
}
