<?php

namespace Tonight\Server;

class Request {

	public const GET = 'GET';
	public const POST = 'POST';
	public const CURRENT_MODE = 'CURRENT';

	private function init($mode) {
		$request = array();
		switch ($mode) {
			case self::GET:
				$request = $_GET;
				break;
			case self::POST:
				$request = $_POST;
				break;
		}

		foreach ($request as $key => $value) {
			$this->{$key} = $value;
		}
	}

	public function __construct($mode = self::CURRENT_MODE) {
		if($mode == self::CURRENT_MODE) {
			$mode = $_SERVER['REQUEST_METHOD'];
		}
		$this->init($mode);
	}

	public function get($key, $default = NULL) {
		return isset($this->{$key}) ? $this->{$key} : $default;
	}

	public function isset($key) {
		return isset($this->{$key});
	}
}