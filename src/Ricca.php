<?php
namespace Hrgruri\Ricca;

use \Hrgruri\Ricca\API\SlackAPI;
use \Hrgruri\Ricca\{RiccaCommand, Response};
use \Hrgruri\Ricca\Exception\{LiteException, CommandException, CronException};

class Ricca
{
    private $slack;
    private $token;
    private $root;
    private $allow;
    private $rc;
    private $botName;
    private $interactive_command    =   null;
    private $interactive_flag       =   false;

    public function __construct(string $dir)
    {
        $this->root     =   rtrim($dir, '/');
        $keys           =   json_decode(file_get_contents("{$this->root}/keys.json"));
        $this->allow    =   json_decode(file_get_contents("{$this->root}/allow.json"));
        $this->token    =   $keys->slack;
        if (!file_exists(getenv("HOME")."/.ricca/data")) {
            mkdir(getenv("HOME")."/.ricca/data", 0700, true);
        }
    }

    public function run()
    {
        $keys           =   json_decode(file_get_contents("{$this->root}/keys.json"));
        $using          =   json_decode(file_get_contents(dirname(__FILE__).'/using.json'));
        $this->slack    =   new SlackAPI($this->token);
        $this->rc       =   new RiccaCommand($keys, $using);
        $this->botName  =   $this->slack->getTokenUser();
        $this->slack->postMsg("Ricca->run() : ".getmypid());
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
            return false;
        }
        $user       =   $this->slack->getUserById($data['user']);
        $channel    =   $this->slack->getChannelById($data['channel']);
        if (!$this->isAllowUser($user)) {
            return false;
        }
        try {
            $text       =   mb_convert_kana($data['text'], 'as');
            //TODO add preg
            if ($text === 'quit') {
                $response = $this->quit();
            } elseif ($this->interactive_flag === true) {
                $command    = $this->interactive_command;
                $response   = $this->rc->fire($command, $text);
            } elseif (preg_match('/^(\S*)(\s.*|)/', $text, $matched) === 1) {
                $command = mb_strtolower($matched[1]);
                $response = $this->rc->fire($command, trim($matched[2]));
            } else {
                throw new LiteException("Not matched");
            }

            // response
            if(is_string($response)) {
                $this->slack->postMsg($response, $channel);
            } elseif ($response instanceof Response) {
                $this->interactive_flag     = $response->flag;
                $this->interactive_command  = $command;
                if (!is_null($response->text) && $response->type === Response::MESSAGE) {
                    $this->slack->postMsg($response->text);
                } elseif (!is_null($response->text) && $response->type === Response::CODE) {
                    $this->response($command, $response->text, $channel);
                }
            }
        } catch (LiteException $e) {
            // $this->slack->postMsg($e->getDetail());
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

    private function isAllowUser(string $name)
    {
        return in_array($name, $this->allow->slack);
    }

    private function response(string $command, string $code, string $channel)
    {
        if (is_null($text = $this->readUserResponse($command, $code))) {
            $text = $this->readDefinedResponse($command, $code);
        }
        if (is_null($text)) {
            throw new CommandException("Undefined Response");
        } else {
            $this->slack->postMsg($text, $channel);
        }
    }

    private function readUserResponse(string $command, string $code) {
        if (file_exists("{$this->root}/response/{$command}.json")) {
            $data = json_decode(file_get_contents("{$this->root}/response/{$command}.json"));
            if (isset($data->{$code})) {
                $text = $data->{$code}[array_rand($data->{$code})];
            }
        }
        return isset($text) ? $text : null;
    }

    private function readDefinedResponse(string $command, string $code) {
        if (file_exists(dirname(__FILE__)."/data/response/{$command}.json")) {
            $data = json_decode(file_get_contents(dirname(__FILE__)."/data/response/{$command}.json"));
            if (isset($data->{$code})) {
                $text = $data->{$code}[array_rand($data->{$code})];
            }
        }
        return isset($text) ? $text : null;
    }

    public function cron()
    {
        if (!file_exists("{$this->root}/cron.json")) {
            throw new CronException("cron.json is not exists");
        }
        $now = explode(' ', date('i G j n w'));
        for ($i = 0; $i < count($now); $i++) {
            $now[$i] = (int)$now[$i];
        }
        $jobs  = json_decode(file_get_contents("{$this->root}/cron.json"));
        $queue = array();
        foreach ($jobs as $tmp) {
            $job = new \Hrgruri\Ricca\Job($tmp, $now);
            if (!$job->checkJob()) {
                throw new CronException();
            }
            if ($job->checkTime()) {
                $queue[] = $job->getMessage();
            }
        }
        if(count($queue) > 0 ) {
            $slack = new SlackAPI($this->token);
            foreach ($queue as $message) {
                $slack->postMsg($message);
            }
        }
    }

    /**
     * quit interactive mode
     */
    private function quit()
    {
        $this->interactive_command  = null;
        $this->interactive_flag     = false;
    }
}
