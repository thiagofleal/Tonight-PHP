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

	public function get($keys = NULL, $default = NULL)
	{
		if ($keys === NULL) {
			return $this->get;
		}
		if (is_string($keys)) {
			if (isset($this->get->{$keys})) {
				return $this->get->{$keys};
			}
			return $default;
		}
		if (is_array($keys) || is_object($keys)) {
			$ret = new stdclass;
			foreach ($keys as $key => $value) {
				if (is_string($key)){
					if (isset($this->get->{$key})) {
						$ret->{$key} = $this->get->{$key};
					} else {
						$ret->{$key} = $value;
					}
				} else {
					if (isset($this->get->{$value})) {
						$ret->{$value} = $this->get->{$value};
					} else {
						$ret->{$value} = $default;
					}
				}
			}
			return $ret;
		}
		return NULL;
	}

	public function post($keys = NULL, $default = NULL)
	{
		if ($keys === NULL) {
			return $this->post;
		}
		if (is_string($keys)) {
			if (isset($this->post->{$keys})) {
				return $this->post->{$keys};
			}
			return $default;
		}
		if (is_array($keys) || is_object($keys)) {
			$ret = new stdclass;
			foreach ($keys as $key => $value) {
				if (is_string($key)){
					if (isset($this->post->{$key})) {
						$ret->{$key} = $this->get->{$key};
					} else {
						$ret->{$key} = $value;
					}
				} else {
					if (isset($this->post->{$value})) {
						$ret->{$value} = $this->get->{$value};
					} else {
						$ret->{$value} = $default;
					}
				}
			}
			return $ret;
		}
		return NULL;
	}

	public function files($keys = NULL, $default = NULL)
	{
		if ($keys === NULL) {
			return $this->files;
		}
		if (is_string($keys)) {
			if (isset($this->files->{$keys})) {
				return $this->files->{$keys};
			}
			return $default;
		}
		if (is_array($keys) || is_object($keys)) {
			$ret = new stdclass;
			foreach ($keys as $key => $value) {
				if (is_string($key)){
					if (isset($this->files->{$key})) {
						$ret->{$key} = $this->files->{$key};
					} else {
						$ret->{$key} = $value;
					}
				} else {
					if (isset($this->files->{$value})) {
						$ret->{$value} = $this->files->{$value};
					} else {
						$ret->{$value} = $default;
					}
				}
			}
			return $ret;
		}
		return NULL;
	}

	public function current($keys = NULL, $default = NULL)
	{
		if ($keys === NULL) {
			return $this->current;
		}
		if (is_string($keys)) {
			if (isset($this->current->{$keys})) {
				return $this->current->{$keys};
			}
			return $default;
		}
		if (is_array($keys) || is_object($keys)) {
			$ret = new stdclass;
			foreach ($keys as $key => $value) {
				if (is_string($key)){
					if (isset($this->current->{$key})) {
						$ret->{$key} = $this->current->{$key};
					} else {
						$ret->{$key} = $value;
					}
				} else {
					if (isset($this->current->{$value})) {
						$ret->{$value} = $this->current->{$value};
					} else {
						$ret->{$value} = $default;
					}
				}
			}
			return $ret;
		}
		return NULL;
	}
}