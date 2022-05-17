<?php

namespace Tonight\Tools;

use json_encode;

class Client
{
    const JSON = "text/json";
    const URL_ENCODED = "application/x-www-form-urlencoded";
    const PLAIN = "text/plain";

    private $type;
    private $headers;
    
    public function __construct($type) {
        $this->type = $type;
        $this->headers = array();
        $this->setHeader("Content-type", $type);
    }

    public function setHeader($header, $value) {
        $this->headers[$header] = $value;
    }

    private function makeBody($body) {
        if ($this->type === self::JSON) {
            return json_encode($body);
        }
        if ($this->type === self::URL_ENCODED) {
            return http_build_query($body);
        }
        return $body;
    }

    public static function sendRequest($url, $method, $headers, $body) {
        $options = array(
            'http' => array(
                'header' => implode("", array_map( function($key, $value) {
                    return "{$key}: {$value}\r\n";
                }, array_keys($headers), $headers)),
                'method'  => $method,
                'content' => $body
            )
        );
        $context = stream_context_create($options);
        return file_get_contents($url, false, $context);
    }

    public function request($url, $method, $body, array $headers = array()) {
        return self::sendRequest($url, $method, array_merge($this->headers, $headers), $body);
    }

    public function get($url, array $headers = array()) {
        return $this->request($url, "GET", array(), $headers);
    }

    public function post($url, $body, array $headers = array()) {
        return $this->request($url, "POST", $this->makeBody($body), $headers);
    }

    public function put($url, $body, array $headers = array()) {
        return $this->request($url, "PUT", $this->makeBody($body), $headers);
    }

    public function delete($url, array $headers = array()) {
        return $this->request($url, "DELETE", array(), $headers);
    }
}