<?php
namespace Hrgruri\Ricca;

class KeyChain
{
    private $keys;

    public function __construct(\stdClass $keys)
    {
        $this->keys = $keys;
    }

    public static function load(string $file)
    {
        return new KeyChain(json_decode(file_get_contents($file)));
    }

    public function get(string $name)
    {
        return $this->keys->$name ?? null;
    }
}
