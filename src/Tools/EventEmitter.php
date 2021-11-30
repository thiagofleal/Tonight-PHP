<?php

namespace Tonight\Tools;

use json_encode;
use json_decode;

class EventEmitter
{
    private $id;
    private $fileName;
    private $dir;
    private $expires = 10;
    private $timeout = NULL;
    private static $baseFileName = "event_{id}.sse";

    public function __construct($id) {
        $this->id = $id;
        $this->fileName = str_replace("{id}", $id, self::$baseFileName);
        $this->dir = sys_get_temp_dir();
    }

    public function setTimeout($timeout) {
        if (is_numeric($timeout)) {
            $this->timeout = intval($timeout);
        } else {
            $this->timeout = NULL;
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
        $content = array(
            "event" => $event,
            "id" => $id,
            "data" => json_encode($data),
            "expires" => time() + $this->expires
        );
        $file = fopen($fileName, "a");
        fwrite($file, json_encode($content).PHP_EOL);
        fclose($file);
    }

    public function subscribe($interval = NULL, array $events = NULL) {
        Session::start();

        $sender = new EventSender(EventSender::TEXT);
        $fileName = "{$this->dir}/{$this->fileName}";
        
        if (is_int($this->timeout)) {
            $sender->setTimeout($this->timeout);
        }
        $sender->register( function($self) use($fileName, $events) {
            $content = "";
            
            if (file_exists($fileName)) {
                $content = file_get_contents($fileName);
            }
            $items = explode(PHP_EOL, trim($content));
            $sent = Session::getOrDefault("sent", array());

            foreach ($items as $key => $item) {
                $json = json_decode($item);

                if (!empty($json->id)) {
                    $event = $json->event;
                    $id = $json->id;
                    $data = $json->data;
                    $expires = intval($json->expires);

                    if ($expires < time()) {
                        unset($items[$key]);
                        continue;
                    }
                    if ($events !== NULL) {
                        if (!in_array($event, $events)) {
                            continue;
                        }
                    }
                    if (!in_array($id, $sent)) {
                        $self->send($event, $id, $data);
                        $sent[] = $id;
                        Session::set("sent", $sent);
                    }
                }
            }
            if (count($items)) {
                file_put_contents($fileName, implode(PHP_EOL, $items).PHP_EOL);
            } else {
                if (file_exists($fileName)) {
                    @unlink($fileName);
                }
            }
        });
        $sender->start($interval);
    }
}