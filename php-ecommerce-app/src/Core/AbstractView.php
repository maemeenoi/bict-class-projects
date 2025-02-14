<?php
/*
	A PHP framework for web sites
	
	Basic view template
	===================
	
	Usage:
		1) The lifetime of a view is as follows:
			a) The view is created by a controller
			b) The controller (usually) sets the model
			c) The controller calls the prepare() method
			d) The controller calls the render() method to print the html
		2) The view has an html template
			a) This is set by the setTemplate() method
			b) The template has replaceable parameters named ##somename##
			c) Replacements should be set by the method setTemplateField() in the 
			   constructor or in the prepare() method.
*/
namespace Agora\Core;

use Agora\Core\Interfaces\IContext;

abstract class AbstractView
{
	private $context;
	private $model;
	private $template;
	private $fields;

	public function __construct(IContext $context)
	{
		$this->context = $context;
		$this->model = null;
		$this->template = null;
		$this->fields = array();
	}
	public function getContext()
	{
		return $this->context;
	}
	public function getModel()
	{
		return $this->model;
	}
	public function setModel($model)
	{
		$this->model = $model;
	}
	public function setTemplate($template)
	{
		$this->template = $template;
	}
	public function setTemplateField($name, $value)
	{
		$this->fields[$name] = $value;
	}
	public function setTemplateFields($fields)
	{
		foreach ($fields as $name => $value) {
			$this->setTemplateField($name, $value);
		}
	}

	public function render()
	{
		if (!file_exists($this->template)) {
			throw new \Exception("Template file not found: " . $this->template);
		}

		$html = file_get_contents($this->template);

		// Process basic field replacements
		foreach ($this->fields as $name => $value) {
			$key = '##' . $name . '##';
			$html = str_replace($key, $value ?? '', $html);
		}

		// Process conditionals
		$html = $this->processConditionals($html);

		// Process foreach loops if you have any
		$html = $this->processForEach($html);

		print $html;
	}

	//	expect subclass to override
	public function prepare()
	{
	}

	protected function processForEach($html)
	{
		$pattern = '/##foreach:(.*?)##(.*?)##endforeach##/s';
		return preg_replace_callback($pattern, function ($matches) {
			$array = $this->fields[$matches[1]] ?? [];
			$template = $matches[2];
			$result = '';

			foreach ($array as $value) {
				$temp = $template;
				$temp = str_replace('##value##', $value, $temp);
				$result .= $temp;
			}

			return $result;
		}, $html);
	}
	protected function processConditionals($html)
	{
		// Process if conditions
		$pattern = '/##if:(.*?)##(.*?)##endif##/s';
		return preg_replace_callback($pattern, function ($matches) {
			$condition = $matches[1];
			$content = $matches[2];

			// Split condition for equals check
			if (strpos($condition, '=') !== false) {
				list($field, $value) = explode('=', $condition);
				return isset($this->fields[$field]) && $this->fields[$field] == $value ? $content : '';
			}

			// Handle negation
			if (strpos($condition, '!') === 0) {
				$field = substr($condition, 1);
				return empty($this->fields[$field]) ? $content : '';
			}

			// Regular condition
			return !empty($this->fields[$condition]) ? $content : '';
		}, $html);
	}
}
