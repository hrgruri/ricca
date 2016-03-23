<?php
namespace Hrgruri\Ricca;

abstract class Command
{
    private $file;
    protected $data;
    protected $user_data;

    /**
    *   @param  string $opt: command option
    *   @param  stdClass | string $key
    *   @return null | string | \Hrgruri\Ricca\Response
    */
    abstract public function run($opt, $key);

    public function __construct($file)
    {
        $dir        = dirname(__FILE__).'/data';
        $this->file = $file;
        $this->data         = (file_exists("{$dir}/command/{$file}") ? json_decode(file_get_contents("{$dir}/command/{$file}")) : null);
        $this->user_data    = (file_exists(getenv("HOME")."/.ricca/data/{$this->file}") ?
            json_decode(file_get_contents(getenv("HOME")."/.ricca/data/{$this->file}")) : null);
    }

    protected function updateUserData()
    {
        file_put_contents(getenv("HOME")."/.ricca/data/{$this->file}", json_encode($this->user_data, JSON_PRETTY_PRINT));
    }

    protected function clearUserData()
    {
        $this->user_data = null;
        if (file_exists(getenv("HOME")."/.ricca/data/{$this->file}")) {
            unlink(getenv("HOME")."/.ricca/data/{$this->file}");
        }
    }
}
