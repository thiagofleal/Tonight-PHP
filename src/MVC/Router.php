<?php

namespace Tonight\MVC;

use stdclass;
use Tonight\Tools\Request;

class Router
{
	public static function getLink(...$args)
	{
		return Config::getBaseUrl() . Config::getRoutesFolder() . implode("/", $args);
	}

	public static function redirect(...$args)
	{
		return header("Location: " . self::getLink(...$args));
	}

	private static function executeRoute($route, $request, $args)
	{
		$str = explode('@', $route);

		if (count($str) == 2) {
			$conName = Config::getControllersNamespace() . "\\" . $str[0];
			$actionName = $str[1];
			$controller = new $conName();
			$controller->$actionName($request, $args);
			return true;
		}
		return false;
	}

	public static function run()
	{
		$url = Config::urlGetter();
		$routes = Config::getRoutes();

		$url = explode('/', $url);
		$request = new Request(Config::getInputProperties());

		foreach ($routes as $route) {
			if (count($route) === 3 && !empty($route[2])) {
				$methods = array();
				if (is_array($route[2])) {
					$methods = $route[2];
				} elseif (is_string($route[2])) {
					$methods = array($route[2]);
				}
				if (!in_array($request->getMethod(), array_map('strtoupper', $methods))) {
					continue;
				}
			}
			$args = new stdclass;
			$urlRoute = explode('/', Config::getRoutesFolder() . $route[0]);

			if (count($url) == count($urlRoute)) {
				for ($i = 0; $i < count($url); $i++) {
					$urlRoute[$i] = trim($urlRoute[$i]);
					if (strpos($urlRoute[$i], '{') === 0) {
						$urlRoute[$i] = str_replace('{', '', $urlRoute[$i]);
						$urlRoute[$i] = str_replace('}', '', $urlRoute[$i]);
						$args->{$urlRoute[$i]} = $url[$i];
						$urlRoute[$i] = $url[$i];
					}
				}
				if (implode('/', $url) == implode('/', $urlRoute)) {
					return self::executeRoute($route[1], $request, $args);
				}
			}
		}
		return self::executeRoute(Config::getNotFoundRoute(), $request, new stdclass);
	}
}
