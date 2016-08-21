<?php
class Core_AuthController extends Zend_Controller_Action
{
	public function loginAction()
	{
		$request = $this->getRequest();
		
		$username = $request->getPost('username');
		$password = $request->getPost('password');
		
		$uri 	 = $request->getRequestUri();
		$sReturn = $request->getScheme().'://'.$request->getHttpHost().'/'.ltrim($uri, '/');
		$sReturn = base64_encode($sReturn);
		
		$this->view->assign('returnUrl',$sReturn);
		
		/**
		 * Redirect to dashboard if user has logged in already
		 */
		$auth = Zend_Auth::getInstance();
		if ($auth->hasIdentity()) {
			$this->_redirect($this->view->serverUrl().$this->view->url(array(), 'core_index_index'));
		}
		if ($request->isPost() && isset($username) && isset($password)) {
			$adapter  = new Core_Services_Auth($username, $password);
			$result   = $auth->authenticate($adapter);
			switch ($result->getCode()) {
				/**
				 * Found user, but the account has not been activated
				 */
				case Core_Services_Auth::NOT_ACTIVE:
					$this->_helper->getHelper('FlashMessenger')
					->addMessage('Account has not been activated');
					$this->_redirect($this->view->serverUrl() . $this->view->url(array(), 'core_auth_login'));
					break;
						
					/**
					 * Logged in successfully
					 */
				case Core_Services_Auth::SUCCESS:
					$user = $auth->getIdentity();
						
					//$this->_redirect($this->view->serverUrl() . $this->view->url(array(), 'core_index_index'));
					$this->_redirect(base64_decode($request->getParam('returnUrl')));
					break;
						
					/**
					 * Not found
					 */
				case Core_Services_Auth::FAILURE_IDENTITY_NOT_FOUND:
				case Core_Services_Auth::FAILURE_CREDENTIAL_INVALID:
				default:
					$this->_helper->getHelper('FlashMessenger')
					->addMessage('User name or password is invalid');
					$this->_redirect($this->view->serverUrl() . $this->view->url(array(), 'core_auth_login'));
					break;
				
			}
		}
	}
	
	public function logoutAction()
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		
		$request = $this->getRequest();
		$sReturn = $this->getRequest()->getParam('returnUrl');
		$iReturn = base64_decode($sReturn);
		
		$auth = Zend_Auth::getInstance();
		if ($auth->hasIdentity()) {
			$user = $auth->getIdentity();
				
			/**
			 * Clear session
 			 */
			Zend_Session::destroy(false, false);
				
			$auth->clearIdentity();
		}
		
		//$this->_redirect($this->view->baseUrl());		
		$this->_redirect($iReturn);		
	}
	
	public function signupAction()
	{
		$request = $this->getRequest();
		
		if ($request->getPost()) {
				
		}
	}
	
	public function profileAction()
	{
		$request = $this->getRequest();
		$username = $request->getParam('user_name');
		
		$userData = Core_Models_User::fetchOne(['user_name' => $username],['user_name'=>1,'first_name'=>1,'last_name'=>1]);
		$this->view->assign('user',$userData);
	}
}