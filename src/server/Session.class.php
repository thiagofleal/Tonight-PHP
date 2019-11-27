<?php

namespace Tonight\Server;

class Session {

	public static function start() {
		session_start();
	}

	public static function set($key, $value) {
		$_SESSION[$key] = $value;
	}

	public static function get($key) {
		return $_SESSION[$key];
	}

	public static function unset($key) {
		unset($_SESSION[$key]);
	}

	public static function setFlash($key, $value) {
		$_SESSION['_Flash'][$key] = $value;
	}

	public static function getFlash($key) {
		$ret = $_SESSION['_Flash'][$key];
		unset($_SESSION['_Flash'][$key]);
		return $ret;
	}
}