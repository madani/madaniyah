<?php
use Pandamp\Utility\Formatting;
class Core_Controllers_Plugin_Auth extends Zend_Controller_Plugin_Abstract
{
	public function preDispatch(Zend_Controller_Request_Abstract $request)
	{
		if ($request->isXmlHttpRequest()) {
			return;
		}
		
		$uri = $request->getRequestUri();
		$uri = strtolower($uri);
		
		$uri = rtrim($uri, '/') . '/';
		if (!(new Formatting)->check_strpos($uri, ['newpost','editpost','panel'])) {
			return;
		}
		
		
		$isAllowed = false;
		
		if (Zend_Auth::getInstance()->hasIdentity()) {
			// for next version
			// @TODO
			$isAllowed = true;
		}
		
		if (!$isAllowed) {
			$forwardAction = Zend_Auth::getInstance()->hasIdentity() ? 'deny' : 'login';
			
			/**
			 * DON'T use redirect! as folow:
			 * $this->getResponse()->setRedirect('/Login/');
			 */
			$request->setModuleName('core')
					->setControllerName('Auth')
					->setActionName($forwardAction)
					->setDispatched(true);
		}
	}
}