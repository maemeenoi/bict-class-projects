<?php
/**	This class is part of a PHP framework for web sites.
 *	It defines the common functionality of all controllers:
 *	- Managing GET and POST methods
 *	- Managing inputs, especially the dangerous super globals
 *	- Rendering a view or redirecting as required
 *
 *	The main function getView is overridden in sub-classes
 *   -- getInput gives access to data from $_POST
 *   -- getFilter gives access to data from $_GET
 *
 *	Reproduction is permitted for educational purposes
 *
 */

namespace Agora\Core;

use Agora\Core\Interfaces\IContext;
use Agora\Core\Exceptions\InvalidRequestException;

abstract class AbstractController
{

	private $context;
	private $redirect;
	private $inputs;
	private $filters;

	public function __construct(IContext $context)
	{
		$this->context = $context;
		$this->redirect = null;
		// Superglobals are dangerous - let's kill some!
		if (isset($_POST)) {
			$this->inputs = $_POST;
			unset($_POST);
		} else {
			$this->inputs = array();
		}
		if (isset($_GET)) {
			$this->filters = $_GET;
			unset($_GET);
		} else {
			$this->filters = array();
		}
		if (isset($_REQUEST)) {
			unset($_REQUEST);
		}
	}
	protected function getContext()
	{
		return $this->context;
	}
	protected function getDB()
	{
		return $this->context->getDB();
	}
	protected function getURI()
	{
		return $this->context->getURI();
	}
	protected function getConfig()
	{
		return $this->context->getConfig();
	}

	public function process()
	{
		$method = $this->getURI()->getRequestMethod();
		switch ($method) {
			case 'GET':
				$view = $this->getView(false);
				break;
			case 'POST':
				$view = $this->getView(true);
				break;
			default:
				throw new InvalidRequestException("Invalid Request verb");
		}
		if ($view !== null) {
			$view->prepare();
			// apply global template arguments
			$site = $this->getURI()->getSite();
			$view->setTemplateField('site', $site);
			$session = $this->context->getSession();
			if ($session->isKeySet('feedback')) {
				$feedback = $session->get('feedback');
				$feedback = "<div class=\"feedback\">$feedback</div>";
				$session->unsetKey('feedback');
			} else {
				$feedback = '';
			}
			$view->setTemplateField('feedback', $feedback);
			$view->render();
		} elseif ($this->redirect !== null) {
			header('Location: ' . $this->redirect);
		} else {
			throw new InvalidRequestException("View not set");
		}
	}

	// sub-controllers will override this
	protected function getView($isPostback)
	{
		return null;
	}

	protected function redirectTo($page, $feedback = null)
	{
		$this->redirect = $this->context->getURI()->getSite() . $page;
		if ($feedback !== null) {
			$this->context->getSession()->set('feedback', $feedback);
		}
	}

	protected function getInput($inputField)
	{
		if (!isset($this->inputs[$inputField])) {
			return null;
		}
		$input = trim($this->inputs[$inputField]);
		return $input; // could do $this->sanitise($input);
	}

	protected function getFilter($filterField)
	{
		if (!isset($this->filters[$filterField])) {
			return null;
		}
		return trim($this->filters[$filterField]);
	}

	// note sanitising is NOT recommended as a general XSS defence
	// (we should encode on output, based on context, not on input)
	// so this is only an example  of possible sanitising
	private function sanitise($input)
	{
		return htmlspecialchars($input, ENT_QUOTES);
	}
}
