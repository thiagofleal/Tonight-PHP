<?php

namespace Tonight\Tools;

use json_encode;
use json_decode;

class EventEmitter
{
    private $id;
    private $fileName;
    private $dir;
    private static $baseFileName = "event_{id}.sse";

    public function __construct($id) {
        $this->id = $id;
        $this->fileName = str_replace("{id}", $id, self::$baseFileName);
        $this->dir = sys_get_temp_dir();
    }

    public function emit($event, $data = array(), $id = NULL) {
        $fileName = "{$this->dir}/{$this->fileName}";
        if ($id === NULL) {
            $id = time().rand(0, 9999);
        }
        $content = array(
            "event" => $event,
            "id" => $id,
            "data" => json_encode($data)
        );
        $file = fopen($fileName, "a");
        fwrite($file, json_encode($content).PHP_EOL);
        fclose($file);
    }

    public function subscribe($interval = NULL, array $events = NULL) {
        Session::start();

        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        @flush();
        @ob_end_flush();
        
        if (!is_int($interval)) {
            $interval = 100;
        }
        $fileName = "{$this->dir}/{$this->fileName}";
        $interval = intval($interval);
        $file = fopen($fileName, "a");
        $sent = array();
        
        if (Session::isset("sent")) {
            $sent = Session::get("sent");
        }
        while (!connection_aborted()) {
            $content = file_get_contents($fileName);
            $items = explode(PHP_EOL, trim($content));
            
            foreach ($items as $item) {
                $json = json_decode($item);

                if (!empty($json->id)) {
                    $event = $json->event;
                    $id = $json->id;
                    $data = $json->data;

                    if ($events !== NULL) {
                        if (!in_array($event, $events)) {
                            break;
                        }
                    }
                    if (!in_array($id, $sent)) {
                        echo "event: ".$event.PHP_EOL;
                        echo "id: ".$id.PHP_EOL;
                        echo "data: ".$data.PHP_EOL;
                        echo PHP_EOL;
                        @flush();
                        @ob_end_flush();
                        $sent[] = $id;
                        Session::set("sent", $sent);
                    }
                }
            }
            usleep($interval * 1000);
        }
        fclose($file);
        unlink($fileName);
    }
}