<?php

namespace Tonight\Tools;

use stdclass;

class Request
{
	private $get;
	private $post;
	private $files;
	private $current;

	public function __construct($props = false)
	{
		if ($props === false) {
			$props = [
				'get' => $_GET,
				'post' => $_POST,
				'files' => $_FILES
			];
		}
		$current = strtolower($_SERVER['REQUEST_METHOD']);
		foreach ($props as $prop => $src) {
			$this->{$prop} = new stdclass;
			foreach ($src as $key => $value) {
				$this->{$prop}->{$key} = $value;
			}
			if ($current == $prop) {
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
}