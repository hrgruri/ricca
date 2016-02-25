<?php
namespace Hrgruri\Ricca;

class SlackAPI
{
    private $token;
    private $members;
    private $channels;
    private $url = 'https://slack.com/api/';

    public function __construct($token)
    {
        $this->token = $token;
        $this->updateMembers();
        $this->updateChannels();
    }

    public function getUserById(string $id)
    {
        $name = null;
        try {
            if (!($key = array_search($id, $this->members))) {
                $this->updateMembers();
                if (!($key = array_search($id, $this->members))) {
                    throw new \Exception();
                }
            }
            $name = $key;
        } catch (\Exception $e) {
            $name = null;
        }
        return $name;
    }

    public function getUserByName(string $name)
    {
        $id = null;
        try {
            if (!array_key_exists($name, $this->members)){
                $this->updateMembers();
                if (!array_key_exists($name, $this->members)){
                    throw new \Exception();
                }
            }
            $id = $this->members[$name];
        } catch (\Exception $e) {
            $id = null;
        }
        return $id;
    }

    public function getChannelById(string $id)
    {
        $name = null;
        try {
            if (!($key = array_search($id, $this->channels))) {
                $this->updateChannels();
                if (!($key = array_search($id, $this->channels))) {
                    throw new \Exception();
                }
            }
            $name = $key;
        } catch (\Exception $e) {
            $name = null;
        }
        return $name;
    }

    public function getChannelByName(string $name)
    {
        $id = null;
        try {
            if (!array_key_exists($name, $this->channels)){
                $this->updateMembers();
                if (!array_key_exists($name, $this->channels)){
                    throw new \Exception();
                }
            }
            $id = $this->channels[$name];
        } catch (\Exception $e) {
            $id = null;
        }
        return $id;
    }

    public function updateMembers()
    {
        $url = "{$this->url}users.list?token={$this->token}";
        $data = json_decode((file_get_contents($url)));
        if ($data->ok == true) {
            $members = array();
            foreach ($data->members as $user) {
                $members[$user->name] = $user->id;
            }
            $this->members = $members;
        }
    }

    public function updateChannels()
    {
        $url = "{$this->url}channels.list?token={$this->token}";
        $data = json_decode((file_get_contents($url)));
        if ($data->ok == true) {
            $channels = array();
            foreach ($data->channels as $channel) {
                $channels[$channel->name] = $channel->id;
            }
            $this->channels = $channels;
        }
    }

    // return user_id
    public function getTokenUser()
    {
        $res = null;
        $url = "{$this->url}auth.test?token={$this->token}";
        $data = json_decode((file_get_contents($url)));
        if ($data->ok == true) {
            $res = $data->user_id;
        }
        return $res;
    }
    public function postMsg(string $msg, $channel = 'general')
    {
        $res = false;
        try {
            $cid = $this->getChannelByName($channel);
            if (is_null($cid)) {
                throw new \Exception();
            }
            $msg = rawurlencode($msg);
            $url = "{$this->url}chat.postMessage?token={$this->token}&channel={$cid}&text=$msg&as_user=true";
            $data = json_decode(file_get_contents($url));
            if (!isset($data->ok) || $data->ok != true) {
                throw new \Exception();
            } else {
                $res = true;
            }
        } catch (\Exception $e) {
            $re = false;
        }
        return $res;
    }
}
