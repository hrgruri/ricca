<?php
namespace Hrgruri\Ricca;

abstract class Command
{
    private $file;
    protected $data;
    protected $user_data;

    /**
    *   @param  string $opt: message
    *   @param  stdClass | string $key
    *   @return null | string | \Hrgruri\Ricca\Response
    */
    abstract public function run($opt, $key);

    public function __construct($file)
    {
        $dir        = dirname(__FILE__).'/data';
        $this->file = $file;
        $this->data         = (file_exists("{$dir}/command/{$file}") ? json_decode(file_get_contents("{$dir}/command/{$file}")) : null);
        $this->user_data    = (file_exists("{$dir}/user/{$file}") ? json_decode(file_get_contents("{$dir}/user/{$file}")) : null);
    }

    protected function updateJson()
    {
        file_put_contents(dirname(__FILE__)."/data/user/{$this->file}", $this->user_data, JSON_PRETTY_PRINT);
    }
}