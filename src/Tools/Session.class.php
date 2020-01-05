<?php

namespace Tonight\Tools;

class Session
{
	public static function start()
	{
		session_start();
	}

	public static function set($key, $value)
	{
		$_SESSION[$key] = $value;
	}

	public static function get($key = NULL)
	{
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
		unset($_SESSION[$key]);
	}

	public static function isset($key)
	{
		return isset($_SESSION[$key]);
	}

	public static function setEmpty($key, $value)
	{
		if (!isset($_SESSION[$key])) {
			$_SESSION[$key] = $value;
		}
	}

	public static function setFlash($key, $value)
	{
		$_SESSION['_Flash'][$key] = $value;
	}

	public static function issetFlash($key)
	{
		return isset($_SESSION['_Flash'][$key]);
	}

	public static function getFlash($key = NULL)
	{
		if ($key !== NULL) {
			$ret = $_SESSION['_Flash'][$key];
			unset($_SESSION['_Flash'][$key]);
			return $ret;
		}
		$flash = new self;

		foreach ($_SESSION['_Flash'] as $key => $value) {
			$flash->{$key} = $value;
		}
		unset($_SESSION['_Flash']);
		return $flash;
	}
}