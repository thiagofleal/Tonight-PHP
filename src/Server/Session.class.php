<?php

namespace Tonight\Server;

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

	public static function get($key)
	{
		return $_SESSION[$key];
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
		if (!isset($_SESSION[$key])){
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

	public static function getFlash($key)
	{
		$ret = $_SESSION['_Flash'][$key];
		unset($_SESSION['_Flash'][$key]);
		return $ret;
	}
}