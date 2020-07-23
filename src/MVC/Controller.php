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
}