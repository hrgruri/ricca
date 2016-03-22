<?php
namespace Hrgruri\Ricca\Command;

use \Hrgruri\Ricca\Exception\CommandException;
use \Hrgruri\Ricca\Response;

class Todo extends \Hrgruri\Ricca\Command
{

    public function run($opt, $key)
    {
        $result = null;
        if (preg_match('/([a-z]+)(|\s.*)$/', $opt, $matched) === 1) {
            // var_dump($matched);
            if (method_exists($this, "{$matched[1]}Todo")) {
                $func   = "{$matched[1]}Todo";
                $result = $this->{$func}(trim($matched[2]));
            }
        }
        return $result;
    }

    private function addTodo($opt)
    {
        $this->user_data[] = $opt;
        $this->updateUserData();
        return new Response('add');
    }

    private function clearTodo($opt)
    {
        $this->clearUserData();
        return new Response('clear');
    }

    private function delTodo($opt)
    {
        $result = null;
        if (count($this->user_data) > 0) {
            $opt    = (int)$opt;
            $i      = 1;
            foreach ($this->user_data as $val) {
                if ($i === $opt) {
                    unset($this->user_data[$i-1]);
                    $this->user_data = array_values($this->user_data);
                    $this->updateUserData();
                    $result = new Response('del');
                    break;
                }
                $i++;
            }
        }
        return is_null($result)? new Response('undel'): $result;
    }

    private function deleteTodo($opt)
    {
        return $this->delTodo($opt);
    }

    private function listTodo($opt)
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
            return new Response('empty');
        }
        return $result;
    }
}
