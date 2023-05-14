<?php

namespace Tonight\Channel;

use json_decode;

class Stream
{
  private $dir = "/tmp";
  private $sessionId;
  private $path;
  private $timeout = false;
  private $listeners = array();

  public static function open($sessionId, array $opts = array()) {
    return new static($sessionId, $opts);
  }

  protected function __construct($sessionId, array $opts) {
    $this->sessionId = $sessionId;

    if (isset($opts["dir"])) {
      if (is_string($opts["dir"])) {
        $this->dir = $opts["dir"];
      }
    }
    $this->path = "{$this->dir}/{$this->sessionId}";

    $this->init();
  }

  private function init() {
    if (!is_dir($this->path)) {
      mkdir($this->path, 0777, true);
    }
  }

  public function setTimeout($timeout) {
    if (is_int($timeout)) {
      $this->timeout = $timeout;
    } else {
      $this->timeout = false;
    }
  }

  public function listen($event, callable $callback) {
    $this->listeners[$event] = $callback;
  }

  public function start($interval = 100) {
    $ends = false;

    if ($this->timeout) {
      $ends = time() + $this->timeout;
    }
    while (!connection_aborted()) {
      if ($ends) {
        if (time() >= $ends) {
          break;
        }
      }
      $files = glob("{$this->path}/*");

      if ($files) {
        foreach ($files as $file) {
          $content = file_get_contents($file);
          unlink($file);
          $json = json_decode($content);

          if (!isset($json->expires) || time() < $json->expires) {
            $event = $json->event;

            if (isset($this->listeners[$event])) {
              $callback = $this->listeners[$event];
              call_user_func($callback, isset($json->data) ? $json->data : "");
            }
          }
        }
      }
      usleep($interval * 1000);
    }
  }
}
