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
	private static $baseRoutesFolder = '';
	private static $routes = array();
	private static $notFoundRoute = '';
	private static $inputProperties = false;

	public static function getBaseUrl() { return self::$baseUrl; }
	public static function getModelsNamespace() { return self::$modelsNamespace; }
	public static function getViewsExtension() { return self::$viewsExtension; }
	public static function getViewsPath() { return self::$viewsPath; }
	public static function getTemplatesPath() { return self::$templatesPath; }
	public static function getControllersNamespace() { return self::$controllersNamespace; }
	public static function getRoutesFolder() { return self::$baseRoutesFolder; }
	public static function getRoutes() { return self::$routes; }
	public static function getRoute($key) { return self::$routes[$key]; }
	public static function getNotFoundRoute() { return self::$notFoundRoute; }
	public static function getInputProperties() { return self::$inputProperties; }

	public static function urlGetter() {
		$func = self::$urlGetter;
		return $func();
	}

	public static function setBaseUrl($baseUrl)
	{
		self::$baseUrl = $baseUrl;
	}

	public static function setUrlGetter(callable $urlGetter)
	{
		self::$urlGetter = $urlGetter;
	}

	public static function setModelsNamespace($modelsNamespace)
	{
		self::$modelsNamespace = $modelsNamespace;
	}

	public static function setViewsExtension($viewsExtension)
	{
		self::$viewsExtension = $viewsExtension;
	}

	public static function setViewsPath($viewsPath)
	{
		self::$viewsPath = $viewsPath;
	}

	public static function setTemplatesPath($templatesPath)
	{
		self::$templatesPath = $templatesPath;
	}

	public static function setControllersNamespace($controllersNamespace)
	{
		self::$controllersNamespace = $controllersNamespace;
	}

	public static function setRoutesFolder($folder)
	{
		self::$baseRoutesFolder = $folder;
	}

	public static function setRoutes(array $routes)
	{
		self::$routes = $routes;
	}

	public static function setNotFoundRoute($notFoundRoute)
	{
		self::$notFoundRoute = $notFoundRoute;
	}

	public static function setInputProperties($inputProperties)
	{
		self::$inputProperties = $inputProperties;
	}

	public static function addRoute($url, $respond)
	{
		self::$routes[] = array($url, $respond);
	}
}