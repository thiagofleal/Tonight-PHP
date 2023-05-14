<?php

namespace Tonight\Channel;

use json_encode;

class Emitter
{
  private $dir = "/tmp";
  private $sessionId;
  private $path;

  public static function create($sessionId, array $opts = array()) {
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
  }

  public function emit($event, $data, array $opts = array()) {
    if (!is_dir($this->path)) {
      mkdir($this->path, 0777, true);
    }
    if (isset($opts["expires"])) {
      $opts["expires"] = time() + $opts["expires"];
    }
    $content = json_encode([
      'event' => $event,
      'data' => $data,
      ...$opts
    ]);
    $filename = "{$this->path}/".floor(microtime(true) * 1000);
    file_put_contents($filename, $content);
  }
}
