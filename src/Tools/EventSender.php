<?php

namespace Tonight\Tools;

use json_encode;

class EventSender {
    private $formatter;
    private $callback;
    private $atExit;
    private $timeout = false;

    const TEXT = 0;
    const JSON = 1;
    
    public function __construct($formatter) {
        if (!is_callable($formatter)) {
            switch($formatter) {
                case self::TEXT:
                    $formatter = function($value) { return $value; };
                    break;
                case self::JSON:
                    $formatter = function($value) { return json_encode($value); };
                    break;
                default:
                    $formatter = function () { return ""; };
            }
        }
        $this->formatter = $formatter;
        $this->callback = null;
        $this->atExit = null;
    }

    public function register(callable $callback) {
        $this->callback = $callback;
    }

    public function onExit(callable $callback) {
        $this->atExit = $callback;
    }

    public function getTimeout() {
        return $this->timeout;
    }

    public function setTimeout($timeout) {
        if (is_int($timeout)) {
            $this->timeout = $timeout;
        } else {
            $this->timeout = false;
        }
    }

    private function flush() {
        ob_flush();
        flush();
    }

    public function send(string $event, string $id, $data) {
        $formatter = $this->formatter;

        echo "event: ".$event.PHP_EOL;
        echo "id: ".$id.PHP_EOL;
        echo "data: ".$formatter($data).PHP_EOL;
        echo PHP_EOL;
        $this->flush();
    }

    public function start($interval = NULL) {
        if (! headers_sent()) {
            header_remove("content-type");
            header_remove("cache-control");
        }
        while (ob_get_level()) {
            ob_end_clean();
        }
        ob_start();
        header('Content-Type: text/event-stream; charset=utf-8');
        header('Cache-Control: no-cache');
        $this->flush();

        if (!is_int($interval)) {
            $interval = 100;
        }
        $interval = intval($interval);
        $count = 0;
        $callback = $this->callback;
        $exit = $this->atExit;
        $ends = false;
        
        if ($this->timeout) {
            $ends = time() + $this->timeout;
        }
        while (!connection_aborted() && (!$ends || time() < $ends)) {
            if (is_callable($callback)) {
                $callback($this, $count++);
            }
            $this->flush();
            usleep($interval * 1000);
        }
        if (is_callable($exit)) {
            $exit();
        }
        ob_end_flush();
    }
}
