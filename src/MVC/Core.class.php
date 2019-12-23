<?php

namespace Tonight\MVC;

use Tonight\Server\Request;

class Core {

	private $baseUrl = '';
	private $arg = 'url';
	private $defaultPage = 'home';
	private $defaultController = 'defaultController';
	private $defaultAction = 'index';
	private $namespace = '';
	private $sufix = 'Controller';

	public function __construct(string $url = NULL) {
		$this->baseUrl = $url;
	}

	public function baseUrl(string $baseUrl = NULL) {
		if(!empty($baseUrl)) {
			$this->baseUrl = $baseUrl;
		}
		return $this->baseUrl;
	}

	public function arg(string $arg = NULL) {
		if(!empty($arg)) {
			$this->arg = $arg;
		}
		return $this->arg;
	}

	public function defaultPage(string $defaultPage = NULL) {
		if(!empty($defaultPage)) {
			$this->defaultPage = $defaultPage;
		}
		return $this->defaultPage;
	}

	public function defaultController(string $defaultController = NULL) {
		if(!empty($defaultController)) {
			$this->defaultController = $defaultController;
		}
		return $this->defaultController;
	}

	public function defaultAction(string $defaultAction = NULL) {
		if(!empty($defaultAction)) {
			$this->defaultAction = $defaultAction;
		}
		return $this->defaultAction;
	}

	public function namespace(string $namespace = NULL) {
		if(!empty($namespace)) {
			$this->namespace = $namespace;
		}
		return $this->namespace;
	}

	public function sufix(string $sufix = NULL) {
		if(!empty($sufix)) {
			$this->sufix = $sufix;
		}
		return $this->sufix;
	}

	public function getLink(...$args) {
		return $this->baseUrl . '/' . implode("/", $args);
	}

	public function run() {
		$request = new Request(Request::GET);
		$url = $request->get($this->arg, $this->defaultPage);
		$arg = explode("/", $url);
		$controller = array_shift($arg).$this->sufix;
		$action = isset($arg[0]) ? array_shift($arg) : $this->defaultAction;
		$controller = $this->namespace."\\".$controller;

		if(!class_exists($controller) || !method_exists($controller, $action)) {
			$controller = $this->namespace."\\".$this->defaultController;
			$action = $this->defaultAction;
		}

		$controller = new $controller();
		return call_user_func(array($controller, $action), $arg);
	}
}