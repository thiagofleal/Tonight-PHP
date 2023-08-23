<?php

namespace Tonight\MVC;

class Controller
{
	private $view;
	private $variables;

	public function __construct()
	{
		$this->variables = array();
	}

	protected function setVariable($key, $value)
	{
		$this->variables[$key] = $value;
	}

	protected function setVariablesArray(array $variables)
	{
		$this->variables = $variables;
	}

	protected function unsetVariable($key)
	{
		unset($this->variables[$key]);
	}

	protected function getVariable($key)
	{
		if (isset($this->variables[$key])) {
			return $this->variables[$key];
		}
		return false;
	}

	protected function setView($view)
	{
		$this->view = Config::getViewsPath() . '/' . $view . '.' . Config::getViewsExtension();
	}

	protected function content()
	{
		extract($this->variables);
		return require $this->view;
	}

	private function getTemplateFrom($template)
	{
		return Config::getTemplatesPath() . '/' . $template . '.' . Config::getViewsExtension();
	}

	protected function render($page, $template = NULL)
	{
		$this->setView($page);
		extract($this->variables);
		if ($template) {
			return require $this->getTemplateFrom($template);
		}
		return $this->content();
	}

	protected function contentType(string $mime)
	{
		header("Content-Type: ".$mime);
	}

	private static function toJson(array $src)
	{
		$array = array();

		foreach ($src as $key => $value) {
			if (is_array($value)) {
				$array[$key] = json_decode(self::toJson($value));
			} else {
				$array[$key] = $value;
			}
		}
		return json_encode($array);
	}

	protected function printJson()
	{
		return print(self::toJson($this->variables));
	}

	protected function httpResponseCode($code)
	{
		return http_response_code($code);
	}
}