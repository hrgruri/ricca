<?php
namespace Hrgruri\Ricca;
class Application
{
    private $path;
    private $commands;
    private $keychain;
    private $loop;
    private $bot_id;
    private $allows;
    private $admins;
    private $channel;
    private static $conn; // Twitter connection

    /**
     *
     * @param string $path
     * @param string $channel
     * @param bool   $notification
     */
    public function __construct(string $path, string $channel = null, bool $notification = null)
    {
        $this->path     = rtrim($path, '/');
        $this->channel  = $channel ?? 'general';
        $this->commands = [];
        $this->keychain = KeyChain::load("{$this->path}/key.json");
        $this->add(new \Hrgruri\Ricca\Command\Pid());
        $this->loop   = \React\EventLoop\Factory::create();
        $this->client = new \Slack\RealTimeClient($this->loop);
        $this->client->setToken($this->keychain->get('slack'));
        $this->init($notification ?? true);
    }

    /**
     * initialization
     * @param  bool   $notification
     */
    private function init(bool $notification)
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
        $this->client->connect()->then(function () use ($data, $notification) {
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

            if ($notification === true) {
                $this->sendMsg('Ricca is online');
            }
        });

        if (!is_dir("{$this->path}/storage")) {
            mkdir("{$this->path}/storage");
        }

        $this->add(new Command\Pid());
    }

    public function run()
    {
        $this->client->on('message', function ($data) {
            if ($this->bot_id !== $data['user']
                && $this->isAllow($data['user'] ?? '')
                && isset($data['text'])
                && preg_match('/^(\S*)(\s.*|)/', $data['text'], $matched) === 1
            ) {
                $storage = null;
                $name    = mb_strtolower($matched[1]);
                if (!isset($this->commands[$name])) {
                    return;
                }
                $command = $this->commands[$name];
                if (file_exists("{$this->path}/storage/{$name}.json")) {
                    $storage = json_decode(file_get_contents("{$this->path}/storage/{$name}.json"));
                }
                $response = $command->execute(
                    new Request(trim(mb_substr($data['text'], strlen($name))), $storage),
                    new Response
                );
                if (is_string($response)) {
                    $this->sendMsg($response);
                } elseif ($response instanceof Response) {
                    $this->processResponse($command, $response);
                }
            }
        });
        $this->loop->run();
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
     */
    private function sendMsg(string $text)
    {
        $this->client->getChannelByName($this->channel)->then(function ($channel) use ($text) {
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
    private function isAllow(string $id) : bool
    {
        return ($this->isAdmin($id) || in_array($id, $this->allows));
    }

    /**
     * save command data
     * @param  string $command command name
     * @param  mixed  $data
     * @return int
     */
    private function save(string $name, $data) : int
    {
        return file_put_contents(
            "{$this->path}/storage/{$name}.json",
            json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );
    }

    /**
     * read command data
     * @param  string $name command name
     * @return mixed
     */
    private function read(string $name)
    {
        return json_decode(file_get_contents("{$this->path}/storage/{$name}.json"));
    }

    private function processResponse(Command $command, Response $res)
    {
        // process withClear
        if (!is_null($res->clear)
            && $res->clear === true
            && file_exists("{$this->path}/storage/{$command->getName()}.json")
        ) {
            unlink("{$this->path}/storage/{$command->getName()}.json");
        }

        // process withData
        if (!is_null($res->data)) {
            $this->save($command->getName(), $res->data);
        }

        // process withText
        if (!is_null($res->text)) {
            $this->sendMsg($res->text);
        }

        // process withTweet
        if (is_string($res->tweet)) {
            $this->tweet($res->tweet);
        }
    }

    private function tweet(string $text)
    {
        if (!isset(self::$conn)) {
            $key = $this->keychain->get('twitter');
            self::$conn = new \Abraham\TwitterOAuth\TwitterOAuth(
                $key->consumer_key       ?? '',
                $key->consumer_secret    ?? '',
                $key->oauth_token        ?? '',
                $key->oauth_token_secret ?? ''
            );
        }
        self::$conn->post('statuses/update', ['status' => $text]);
    }
}
