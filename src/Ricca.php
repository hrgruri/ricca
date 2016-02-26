<?php
namespace Hrgruri\Ricca;

use \Hrgruri\Ricca\{SlackAPI, RiccaCommand};
use \Hrgruri\Ricca\{LiteException, CommandException};

class Ricca
{
    private $slack;
    private $token;
    private $root;
    private $allow;
    private $rc;
    private $botName;

    public function __construct(string $dir)
    {
        $this->root     =   $dir;
        $keys           =   json_decode(file_get_contents("{$this->root}/keys.json"));
        $aliases        =   json_decode(file_get_contents(dirname(__FILE__).'/alias.json'));
        $this->allow    =   json_decode(file_get_contents("{$this->root}/allow.json"));
        $this->token    =   $keys->slack;
        $this->slack    =   new \Hrgruri\Ricca\SlackAPI($this->token);
        $this->rc       =   new RiccaCommand($keys, $aliases);
        $this->botName  =   $this->slack->getTokenUser();
        $this->slack->postMsg("Ricca->run() : ".getmypid());
    }

    public function run()
    {
        $loop  = \React\EventLoop\Factory::create();
        $client = new \Slack\RealTimeClient($loop);
        $client->setToken($this->token);
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
        $result = false;
        if ($this->botName === $data['user']) {
            return true;
        }
        $user       =   $this->slack->getUserById($data['user']);
        $channel    =   $this->slack->getChannelById($data['channel']);
        if (!$this->isAllowUser($user)) {
            return false;
        }
        try {
            $text       =   mb_convert_kana($data['text'], 'as');
            // ALL CHANNELS
            if (preg_match('/^(\S*)(\s.*|)/', $text, $matched) === 1) {
                $cmd = $matched[1];
                $res = $this->rc->fire($cmd, trim($matched[2]));
            } else {
                throw new CommandException("Not matched");
            }
            if ($res->flg === true) {
                if(is_null($res->msg)) {
                    $this->response(lcfirst($cmd), $channel);
                } else {
                    $this->slack->postMsg($res->msg, $channel);
                }
                $result = true;
            } else {

            }
        } catch (LiteException $e) {
            $this->slack->postMsg($e->getDetail());
            $result = false;
        } catch (CommandException $e){
            $this->slack->postMsg($e->getDetail());
            $result = false;
        } catch (\Exception $e) {
            $this->slack->postMsg($e->getMessage());
            $result = false;
        }
        return $result;
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
