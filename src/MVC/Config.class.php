<?php

namespace Tonight\MVC;

class Config
{
	private static $baseUrl = '';
	private static $urlGetter = NULL;
	private static $modelsNamespace = '';
	private static $viewsPath = '';
	private static $viewsExtension = 'php';
	private static $templatesPath = '';
	private static $controllersNamespace = '';
	private static $routes = array();
	private static $notFoundRoute = '';

	public static function getBaseUrl() { return self::$baseUrl; }
	public static function getModelsNamespace() { return self::$modelsNamespace; }
	public static function getViewsExtension() { return self::$viewsExtension; }
	public static function getViewsPath() { return self::$viewsPath; }
	public static function getTemplatesPath() { return self::$templatesPath; }
	public static function getControllersNamespace() { return self::$controllersNamespace; }
	public static function getRoutes() { return self::$routes; }
	public static function getRoute($key) { return self::$routes[$key]; }
	public static function getNotFoundRoute() { return self::$notFoundRoute; }

	public static function urlGetter() {
		$func = self::$urlGetter;
		return $func();
	}

	public static function setBaseUrl(string $baseUrl)
	{
		self::$baseUrl = $baseUrl;
	}

	public static function setUrlGetter(callable $urlGetter)
	{
		self::$urlGetter = $urlGetter;
	}

	public static function setModelsNamespace(string $modelsNamespace)
	{
		self::$modelsNamespace = $modelsNamespace;
	}

	public static function setViewsExtension(string $viewsExtension)
	{
		self::$viewsExtension = $viewsExtension;
	}

	public static function setViewsPath(string $viewsPath)
	{
		self::$viewsPath = $viewsPath;
	}

	public static function setTemplatesPath(string $templatesPath)
	{
		self::$templatesPath = $templatesPath;
	}

	public static function setControllersNamespace(string $controllersNamespace)
	{
		self::$controllersNamespace = $controllersNamespace;
	}

	public static function setRoutes(array $routes)
	{
		self::$routes = $routes;
	}

	public static function setNotFoundRoute(string $notFoundRoute)
	{
		self::$notFoundRoute = $notFoundRoute;
	}

	public static function addRoute(string $url, string $respond)
	{
		self::$routes[] = array($url, $respond);
	}
}