<?php
namespace Hrgruri\Ricca;

use \Hrgruri\Ricca\API\SlackAPI;
use Zend\Http\Header\Warning;
use \Hrgruri\Ricca\Response;
use \Hrgruri\Ricca\Exception\{RiccaException, WarningException};
use \Hrgruri\Ricca\Exception\{CommandException, CronException};

class Ricca
{
    const   SPECIAL_COMMAND = ['quit'];
    const   RICCA_COMMAND   = ['start', 'stop', 'exit'];
    private $slack;
    private $token;
    private $root;
    private $allow;
    private $keys;
    private $using;
    private $botName;
    private $interactive_command    =   null;
    private $interactive_flag       =   false;
    private $active_flag = true;

    public function __construct(string $dir)
    {
        set_error_handler( function ($errno, $errstr, $errfile, $errline, $errcontext) {
            throw new WarningException($errstr, 0, $errno, $errfile, $errline);
        }, E_WARNING);
        $this->root     =   rtrim($dir, '/');
        if (!file_exists("{$this->root}/keys.json") || !file_exists("{$this->root}/allow.json")) {
            print "ERROR: not found keys.json or allow.json".PHP_EOL;
            $this->exit();
        }
        $keys           =   json_decode(file_get_contents("{$this->root}/keys.json"));
        $this->allow    =   json_decode(file_get_contents("{$this->root}/allow.json"));
        if (!isset($keys->slack)|| is_null($this->allow) ) {
            print "ERROR: __construct()".PHP_EOL;
            $this->exit();
        }
        $this->token    =   $keys->slack;
        if (!file_exists(getenv("HOME")."/.ricca/data")) {
            mkdir(getenv("HOME")."/.ricca/data", 0700, true);
        }
    }

    public function run()
    {
        $this->keys     =   json_decode(file_get_contents("{$this->root}/keys.json"));
        $this->using    =   file_exists(dirname(__FILE__).'/using.json') ? json_decode(file_get_contents(dirname(__FILE__).'/using.json')) : [];
        $this->slack    =   new SlackAPI($this->token);
        $this->botName  =   $this->slack->getTokenUser();
        $this->slack->postMsg("Ricca->run() : ".getmypid());
        $loop   = \React\EventLoop\Factory::create();
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
            $response   = null;
            $text       = mb_convert_kana($data['text'], 'as');
            if (is_string($tmp = $this->botResponse($text))) {
                $response = (new Response)->message($tmp);
            } elseif (in_array($text, self::SPECIAL_COMMAND) && $this->active_flag === true) {
                $this->interactive_flag     = false;
                $response   = $this->{$text}();
                $command    = 'ricca';
            } elseif (preg_match('/^ricca\s([a-z]*)(|\s\S*)$/i', $text, $matched) === 1) {
                $tmp_command = mb_strtolower($matched[1]);
                if (in_array($tmp_command,self::RICCA_COMMAND)){
                    $this->interactive_flag     = false;
                    $response   = $this->{$tmp_command}(trim($matched[2]));
                    $command    = 'ricca';
                }
            } elseif ($this->interactive_flag === true && $this->active_flag === true) {
                $command    = $this->interactive_command;
                $response   = $this->runCommand($command, $text);
            } elseif (preg_match('/^(\S*)(\s.*|)/', $text, $matched) === 1 && $this->active_flag === true) {
                $command    = mb_strtolower($matched[1]);
                $response   = $this->runCommand($command, trim($matched[2]));
            }

            // response
            if(is_string($response)) {
                $this->slack->postMsg($response, $channel);
                $result = true;
            } elseif ($response instanceof Response) {
                $this->interactive_flag     = $response->flag;
                $this->interactive_command  = $command ?? '';
                if (!is_null($response->text) && $response->type === Response::MESSAGE) {
                    $this->slack->postMsg($response->text);
                } elseif (!is_null($response->text) && $response->type === Response::CODE) {
                    $this->response($command, $response->text, $channel);
                }
                $result = true;
            }
        } catch (RiccaException $e) {
            $this->response('ricca', $e->code, $channel);
        } catch (CommandException $e){
            $this->slack->postMsg($e->getDetail());
        } catch (\Exception $e) {
            $this->slack->postMsg($e->getMessage());
        }
        return $result;
    }

    /**
     * @param  string  $name username
     * @return boolean
     */
    private function isAllowUser(string $name)
    {
        return in_array($name, $this->allow);
    }

    /**
     * @param  string $command command name
     * @param  string $text    command option
     * @return null | string | \Hrgruri\Ricca\Response
     */
    private function runCommand(string $command, string $text) {
        $result = null;
        $class = '\\Hrgruri\\Ricca\\Command\\'.ucfirst($command);
        if (class_exists($class)) {
            $key = \Hrgruri\Ricca\KeyChain::getKey($this->keys, $this->using, $command);
            $result = (new $class("{$command}.json"))->run($text, $key);
        }
        return $result;
    }

    private function response(string $command, string $code, string $channel)
    {
        if (is_null($text = $this->readUserResponse($command, $code))) {
            $text = $this->readDefinedResponse($command, $code);
        }
        if (is_null($text)) {
            $this->slack->postMsg("Undefined Response\nCommand = {$command}\nCode = {$code}");
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
     * @param  string $text
     * @return null | string
     */
    private function botResponse(string $text)
    {
        $result = array('flag' => false, "text" => null);
        if(!file_exists("{$this->root}/bot.json")) {
            $result = null;
        } elseif(is_null($data = json_decode(file_get_contents("{$this->root}/bot.json")))) {
            throw new RiccaException("broken_json", 'bot.json');
        } else {
            try {
                foreach ($data as $val) {
                    if (preg_match($val->pattern, $text) === 1) {
                        $result = $val->response[array_rand($val->response)];
                    }
                }
            } catch (WarningException $e) {
                throw new RiccaException("broken_preg_pattern");
            }
        }
        return $result;
    }

    private function start(string $text)
    {
        $this->active_flag = true;
        return (new Response)->code('ricca_start');
    }

    private function stop(string $text)
    {
        $this->active_flag = false;
        return (new Response)->code('ricca_stop');
    }

    /**
     * quit interactive mode
     */
    private function quit()
    {
        $this->interactive_command  = null;
        $this->interactive_flag     = false;
        return (new Response)->code('quit_interactive');
    }

    private function exit(string $text)
    {
        exit();
    }
}
