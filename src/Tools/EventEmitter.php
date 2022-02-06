<?php

namespace Tonight\Tools;

use json_encode;
use json_decode;
use call_user_func;

class EventEmitter
{
    private $id;
    private $fileName;
    private $dir;
    private $expires = 10;
    private $timeout = false;
    private $filters = array();
    private $map = NULL;
    private static $baseFileName = "event.{event}.{id}.sse";

    public function __construct($name) {
        $this->id = $name;
        $this->fileName = str_replace("{event}", $name, self::$baseFileName);
        $this->dir = sys_get_temp_dir();
    }

    public function setTimeout($timeout) {
        if (is_numeric($timeout)) {
            $this->timeout = intval($timeout);
        } else {
            $this->timeout = false;
        }
    }

    public function getTimeout() {
        return $this->timeout;
    }

    public function getExpires() {
        return $this->expires;
    }

    public function setExpires($expires) {
        if (is_numeric($expires)) {
            $this->expires = intval($expires);
        }
    }

    public function emit($event, $data = array(), $id = NULL) {
        $fileName = "{$this->dir}/{$this->fileName}";
        if ($id === NULL) {
            $id = "".time().rand(0, 9999);
        }
        $fileName = str_replace("{id}", $id, $fileName);
        $content = array(
            "event" => $event,
            "id" => $id,
            "data" => json_encode($data),
            "expires" => time() + $this->expires
        );
        file_put_contents($fileName, json_encode($content).PHP_EOL);
    }

    public function addFilter(callable $filter) {
        $this->filters[] = $filter;
        $index = count($this->filters) - 1;
        return function () use ($index) {
            unset($this->filters[$index]);
        };
    }

    public function setMap(callable $transform) {
        $this->map = $transform;
        return function () {
            $this->map = NULL;
        };
    }

    public function subscribe($interval = NULL) {
        $sender = new EventSender(EventSender::TEXT);
        $fileName = "{$this->dir}/{$this->fileName}";
        $fileName = str_replace("{id}", "*", $fileName);
        $sent = array();
        
        if (is_int($this->timeout)) {
            $sender->setTimeout($this->timeout);
        }
        $sender->register( function($self) use($fileName, &$sent) {
            $content = "";
            $files = glob($fileName);
            
            foreach ($files as $file) {
                $content = file_get_contents($file);
                $json = json_decode($content);

                if (!empty($json->id)) {
                    $event = $json->event;
                    $id = $json->id;
                    $data = $json->data;
                    $expires = intval($json->expires);

                    if ($expires < time()) {
                        @unlink($file);
                        continue;
                    }
                    if (!in_array($id, $sent)) {
                        $ctrl = true;

                        foreach ($this->filters as $filter) {
                            if ($ctrl) {
                                $ctrl = call_user_func($filter, json_decode($data));
                            }
                        }
                        if ($ctrl) {
                            if ($this->map !== NULL) {
                                $data = json_encode(call_user_func($this->map, json_decode($data)));
                            }
                            $self->send($event, $id, $data);
                            $sent[] = $id;
                        }
                    }
                }
            }
        });
        $sender->start($interval);
    }
}