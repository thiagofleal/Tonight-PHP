<?php

namespace Tonight\Tools;

use json_encode;

class EventSender {
    private $formatter;
    private $callback;

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
    }

    public function register(callable $callback) {
        $this->callback = $callback;
    }

    public function send(string $event, string $id, array $data) {
        $formatter = $this->formatter;

        echo "event: ".$event.PHP_EOL;
        echo "id: ".$id.PHP_EOL;
        echo "data: ".$formatter($data).PHP_EOL;
        echo PHP_EOL;
        @flush();
        @ob_end_flush();
    }

    public function start($interval = NULL) {
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        if (!is_int($interval)) {
            $interval = 100;
        }
        $interval = intval($interval);
        $count = 0;
        $callback = $this->callback;

        while (!connection_aborted()) {
            if (is_callable($callback)) {
                $callback($this, $count++);
            }
            usleep($interval * 1000);
        }
    }
}