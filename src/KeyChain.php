<?php
namespace Hrgruri\Ricca;

use \Hrgruri\Ricca\Exception\KeyException;

class KeyChain
{
    public static function getKey($keys, $using, string $command)
    {
        if (property_exists($using, $command)) {
            $key = array();
            foreach ($using->{$command} as $target) {
                $key[$target] = property_exists($keys, $target) ? $keys->{$target} : null;
            }
        } else {
            $key = null;
        }
        return $key;
    }
}
