<?php

namespace Tonight\Tools;

class Session
{
	private static $started = false;

	public static function start()
	{
		if (!self::$started) {
			session_start();
			self::$started = true;
		}
	}

	public static function set($key, $value)
	{
		self::start();
		$_SESSION[$key] = $value;
	}

	public static function get($key = NULL)
	{
		self::start();
		if ($key !== NULL) {
			return $_SESSION[$key];
		}
		$session = new self;

		foreach ($_SESSION as $key => $value) {
			$session->{$key} = $value;
		}
		return $session;
	}

	public static function unset($key)
	{
		self::start();
		unset($_SESSION[$key]);
	}

	public static function isset($key)
	{
		self::start();
		return isset($_SESSION[$key]);
	}

	public static function setEmpty($key, $value)
	{
		self::start();
		if (!isset($_SESSION[$key])) {
			$_SESSION[$key] = $value;
		}
	}

	public static function getFlash($key = NULL)
	{
		self::start();
		if ($key !== NULL) {
			$ret = $_SESSION[$key];
			unset($_SESSION[$key]);
			return $ret;
		}
		$flash = new self;

		foreach ($_SESSION as $key => $value) {
			$flash->{$key} = $value;
			unset($_SESSION[$key]);
		}
		return $flash;
	}
}