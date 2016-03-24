<?php
namespace Hrgruri\Ricca\Command;

use \Hrgruri\Ricca\Exception\CommandException;
use \Hrgruri\Ricca\Response;

class Task extends \Hrgruri\Ricca\Command
{

    public function run($text, $key)
    {
        $result = null;
        if (preg_match('/([a-z]+)(|\s.*)$/', $text, $matched) === 1) {
            // var_dump($matched);
            if (method_exists($this, "{$matched[1]}Task")) {
                $func   = "{$matched[1]}Task";
                $result = $this->{$func}(trim($matched[2]));
            }
        }
        return $result;
    }

    private function addTask($text)
    {
        $this->user_data[] = $text;
        $this->updateUserData();
        return (new Response)->code('add');
    }

    private function clearTask($text)
    {
        $this->clearUserData();
        return (new Response)->code('clear');
    }

    private function delTask($text)
    {
        $result = null;
        if (count($this->user_data) > 0) {
            $id = (int)$text;
            $i  = 1;
            foreach ($this->user_data as $val) {
                if ($i === $id) {
                    unset($this->user_data[$i-1]);
                    $this->user_data = array_values($this->user_data);
                    $this->updateUserData();
                    $result = (new Response)->code('del');
                    break;
                }
                $i++;
            }
        }
        return is_null($result)? (new Response)->code('undel') : $result;
    }

    private function deleteTask($text)
    {
        return $this->delTask($text);
    }

    private function listTask($text)
    {
        $result = null;
        if (count($this->user_data) > 0) {
            $i = 1;
            $result = '';
            foreach ($this->user_data as $val) {
                $result.= "{$i}: $val\n";
                $i++;
            }
        } else {
            return (new Response)->code('empty');
        }
        return $result;
    }
}
