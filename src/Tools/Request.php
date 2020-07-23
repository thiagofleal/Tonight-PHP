<?php

namespace Tonight\Tools;

class Request
{
	public const GET = 'GET';
	public const POST = 'POST';

	public static function modeGet()
	{
		return new self(self::GET);
	}

	public static function modePost()
	{
		return new self(self::POST);
	}

	public static function currentMode()
	{
		return new self($_SERVER['REQUEST_METHOD']);
	}

	private function init($mode)
	{
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

	private function __construct($mode)
	{
		$this->init($mode);
	}

	public function get($key, $default = NULL)
	{
		return isset($this->{$key}) ? $this->{$key} : $default;
	}

	public function isset($key)
	{
		return isset($this->{$key});
	}
}