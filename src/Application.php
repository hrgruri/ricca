<?php
namespace Hrgruri\Ricca;
class Application
{
    private $path;
    private $commands;
    private $keychain;
    private $slack;
    private $loop;
    private $bot_id;
    private $allows;
    private $admins;
    private $channel;

    /**
     *
     * @param string $path
     * @param string $channel
     */
    public function __construct(string $path, string $channel = null)
    {
        $this->path     = rtrim($path, '/');
        $this->channel  = $channel;
        $this->commands = [];
        $this->keychain = KeyChain::load("{$this->path}/key.json");
        $this->add(new \Hrgruri\Ricca\Command\Pid());
        $this->loop   = \React\EventLoop\Factory::create();
        $this->client = new \Slack\RealTimeClient($this->loop);
        $this->client->setToken($this->keychain->get('slack'));
        $this->init();
    }

    private function init()
    {
        $file = "{$this->path}/user.json";
        if (!file_exists($file)) {
            file_put_contents($file,
                json_encode(
                    ['admin' => [], 'allow' => []],
                    JSON_PRETTY_PRINT
                )
            );
        }
        $data = json_decode(file_get_contents($file));
        if (!is_array($data->admin) || !is_array($data->allow)) {
            print "user.json is broken\n";
            exit(1);
        }
        $this->client->connect()->then(function () use ($data) {
            $this->admins   = [];
            $this->allows   = [];

            // set admin id
            foreach ($data->admin as $name) {
                $this->client->getUserByName($name)->then(function ($user){
                    $this->admins[] = $user->getId();
                });
            }

            // set allow user id
            foreach ($data->allow as $name) {
                $this->client->getUserByName($name)->then(function ($user){
                    $this->allows[] = $user->getId();
                });
            }

            // set Bot id
            $this->client->getAuthedUser()->then(function ($user) {
                $this->bot_id = $user->getId();
            });

            if (isset($this->channel)) {
                $this->sendMsg('Ricca is online', $this->channel);
            }
        });

        $this->add(new Command\Pid());
    }

    public function run()
    {
        $this->client->on('message', function ($data) {
            if ($this->bot_id !== $data['user']
                && $this->isAllow($data['user'])
                && isset($data['text'])
                && preg_match('/^(\S*)(\s.*|)/', $data['text'], $matched) === 1
            ) {
                $command = mb_strtolower($matched[1]);
                if (!isset($this->commands[$command])) {
                    return;
                }

                $response = $this->commands[$command]->execute(
                    trim(mb_substr($data['text'], strlen($command)))
                );
                $this->sendMsg($response, $this->commands[$command]->getChannel());
            }
        });
        $this->loop->run();
    }

    /**
     * set Command channel
     * @param  string $command command name
     * @param  string $channel channel name
     * @return bool
     */
    public function channel(string $command, string $channel) : bool
    {
        if (!isset($this->commands[$command])) {
            return false;
        }
        $this->commands[$command]->setChannel($channel);
        return true;
    }

    /**
     * add Command
     * @param  Command $command
     * @return boolean
     */
    public function add(Command $command) : bool
    {
        $name = strtolower($command->getName());
        if (!is_string($name) || isset($this->commands[$name])) {
            return false;
        }
        $this->commands[$name] = $command;
        return true;
    }

    /**
     * get Command list
     * @return array
     */
    public function list() : array
    {
        $result = [];
        foreach ($this->commands as $key => $value) {
            $result[$key] = get_class($value);
        }
        return $result;
    }

    /**
     * send message to Slack
     * @param  string  $text message
     * @param  string  $name channel name
     */
    private function sendMsg(string $text, string $name)
    {
        $this->client->getChannelByName($name)->then(function ($channel) use ($text) {
            $this->client->send($text, $channel);
        });
    }

    /**
     *
     * @param  string $id
     * @return bool
     */
    private function isAdmin(string $id) : bool
    {
        return in_array($id, $this->admins);
    }

    /**
     *
     * @param  string $id
     * @return bool
     */
    private function isAllow($id)
    {
        return ($this->isAdmin($id) || in_array($id, $this->allows));
    }
}
