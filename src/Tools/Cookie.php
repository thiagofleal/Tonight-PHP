<?php

namespace Tonight\Tools;

class Cookie
{
	public static function start()
	{}

	public static function set($key, ...$args)
	{
		setcookie($key, ...$args);
	}

	public static function get($key = NULL)
	{
		if ($key !== NULL) {
			return $_COOKIE[$key];
		}
		$cookie = new self;

		foreach ($_COOKIE as $key => $value) {
			$cookie->{$key} = $value;
		}
		return $cookie;
	}

	public static function unset($key)
	{
		unset($_COOKIE[$key]);
		setcookie($key, NULL, -1);
	}

	public static function isset($key)
	{
		return isset($_COOKIE[$key]);
	}

	public static function setEmpty($key, ...$args)
	{
		if (!isset($_COOKIE[$key])) {
			self::set($value, ...$args);
		}
	}

	public static function getFlash($key = NULL)
	{
		if ($key !== NULL) {
			$ret = self::get($key);
			self::unset($key);
			return $ret;
		}
		$flash = new self;

		foreach ($_COOKIE as $key => $value) {
			$flash->{$key} = $value;
			self::unset($key);
		}
		return $flash;
	}
}