<?php

namespace Tonight\Tools;

use json_encode;

class SSE
{
  public static function start() {
    if (!headers_sent()) {
      header_remove("content-type");
      header_remove("cache-control");
    }
    header('Content-Type: text/event-stream; charset=utf-8');
    header('Cache-Control: no-cache');
    flush();
  }

  public static function emit($event, $data = NULL) {
    echo "event: {$event}" . PHP_EOL;

    if ($data !== NULL) {
      echo "data: ".(is_string($data) || is_numeric($data) ? $data : json_encode($data)).PHP_EOL;
    }
    echo PHP_EOL;
    flush();
  }
}
