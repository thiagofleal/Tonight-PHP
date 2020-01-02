<?php

namespace Tonight\MVC;

use Tonight\Server\Request;

class Router
{
	public static function getLink(...$args)
	{
		return Config::getBaseUrl() . '/' . implode("/", $args);
	}

	public static function redirect(...$args)
	{
		return header("Location: " . self::getLink(...$args));
	}

	private static function executeRoute($route, $args)
	{
		$str = explode('@', $route);
		$request = new Request();

		if (count($str) == 2) {
			$conName = Config::getControllersNamespace() . "\\" . $str[0];
			$actionName = $str[1];
			$controller = new $conName();
			if (count($args)) {
				$controller->$actionName((object)$args, $request);
			}
			else {
				$controller->$actionName($request);
			}
			return true;
		}
		return false;
	}

	public static function run()
	{
		$url = Config::urlGetter();
		$routes = Config::getRoutes();
		$args = array();

		$url = explode('/', $url);

		foreach ($routes as $route) {
			$urlRoute = explode('/', $route[0]);

			if (count($url) == count($urlRoute)) {
				for ($i = 0; $i < count($url); $i++) { 
					if (strpos($urlRoute[$i], '{') === 0) {
						$urlRoute[$i] = str_replace('{', '', $urlRoute[$i]);
						$urlRoute[$i] = str_replace('}', '', $urlRoute[$i]);
						$args[$urlRoute[$i]] = $url[$i];
						$urlRoute[$i] = $url[$i];
					}
				}

				if (implode('/', $url) == implode('/', $urlRoute)) {
					return self::executeRoute($route[1], $args);
				}
			}
		}

		return self::executeRoute(Config::getNotFoundRoute(), $args);
	}
}