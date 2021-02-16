<?php

namespace Tonight\Tools;

use stdclass;

class Request
{
	private $method;
	private $get;
	private $post;
	private $files;
	private $current;
	private $put;
	private $delete;
	private $head;
	private $options;

	public static function allowMethods(string $methods = '*')
	{
		header("Access-Control-Allow-Methods:".$methods);
	}

	public function __construct($props = false)
	{
		if ($props === false) {
			$props = [
				'get' => function() { return $_GET; },
				'post' => function() { return $_POST; },
				'files' => function() { return $_FILES; }
			];
		}
		$this->method = strtolower($_SERVER['REQUEST_METHOD']);
		foreach ($props as $prop => $src) {
			$this->{$prop} = new stdclass;
			$data = $src();
			if (empty($data)) {
				$data = [];
			}
			foreach ($data as $key => $value) {
				$this->{$prop}->{$key} = $value;
			}
			if ($this->method == $prop) {
				$this->current = $this->{$prop};
			}
		}
	}

	protected static function values($src, $keys, $default)
	{
		if ($keys === NULL) {
			return $src;
		}
		if (is_string($keys)) {
			if (!empty($src->{$keys})) {
				return $src->{$keys};
			}
			return $default;
		}
		if (is_array($keys) || is_object($keys)) {
			$ret = new stdclass;
			foreach ($keys as $key => $value) {
				if (is_string($key)){
					if (!empty($src->{$key})) {
						$ret->{$value} = $src->{$key};
					} else {
						$ret->{$value} = $default;
					}
				} else {
					if (!empty($src->{$value})) {
						$ret->{$value} = $src->{$value};
					} else {
						$ret->{$value} = $default;
					}
				}
			}
			return $ret;
		}
		return NULL;
	}

	public function get($keys = NULL, $default = NULL)
	{
		return self::values($this->get, $keys, $default);
	}

	public function post($keys = NULL, $default = NULL)
	{
		return self::values($this->post, $keys, $default);
	}

	public function files($keys = NULL, $default = NULL)
	{
		return self::values($this->files, $keys, $default);
	}

	public function current($keys = NULL, $default = NULL)
	{
		return self::values($this->current, $keys, $default);
	}

	public function put($keys = NULL, $default = NULL)
	{
		return self::values($this->put, $keys, $default);
	}

	public function delete($keys = NULL, $default = NULL)
	{
		return self::values($this->delete, $keys, $default);
	}

	public function head($keys = NULL, $default = NULL)
	{
		return self::values($this->head, $keys, $default);
	}

	public function options($keys = NULL, $default = NULL)
	{
		return self::values($this->options, $keys, $default);
	}

	public function getMethod()
	{
		return strtoupper($this->method);
	}

	public function onGet(callable $callback)
	{
		if ($this->getMethod() === 'GET') {
			$callback($this->get);
		}
	}

	public function onPost(callable $callback)
	{
		if ($this->getMethod() === 'POST') {
			$callback($this->post);
		}
	}

	public function onPut(callable $callback)
	{
		if ($this->getMethod() === 'PUT') {
			$callback($this->put);
		}
	}

	public function onDelete(callable $callback)
	{
		if ($this->getMethod() === 'DELETE') {
			$callback($this->delete);
		}
	}

	public function onHead(callable $callback)
	{
		if ($this->getMethod() === 'HEAD') {
			$callback($this->head);
		}
	}

	public function onOptions(callable $callback)
	{
		if ($this->getMethod() === 'OPTIONS') {
			$callback($this->options);
		}
	}
}