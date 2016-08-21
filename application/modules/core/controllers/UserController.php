<?php
class Core_UserController extends Zend_Controller_Action
{
	public function editAction()
	{
		$request = $this->getRequest();
		$username = $request->getParam('username');
		
		$user = Zend_Auth::getInstance()->getIdentity();
		$userData = Core_Models_User::fetchOne(['user_name' => $username]);
		$this->view->assign('user',$userData);
		//$this->view->assign('user',$user);
		
		if ($request->isPost()) {
			$user->first_name = $request->getPost('first_name');
			$user->last_name  = $request->getPost('last_name');
			$user->location   = $request->getPost('location');
			$user->bio        = $request->getPost('bio');
			$user->website    = $request->getPost('website');
			$user->gender     = $request->getPost('gender');
			$user->address    = [
				'street1'  => $request->getPost('street1'),
				'street2'  => $request->getPost('street2'),
				'city'     => $request->getPost('city'),
				'state'    => $request->getPost('state'),
				'country'  => $request->getPost('country'),
				'zip_code' => $request->getPost('zip_code'),
			];
			
			$birthday         = implode('/', [$request->getPost('month'), $request->getPost('day'), $request->getPost('year')]);
			$user->birthday   = new MongoDate(strtotime(date($birthday)));
			
			unset($user['auth']);
			
			Core_Models_User::update(['_id' => new MongoId($user->getId())],$user->export());
			
			Zend_Auth::getInstance()->getStorage()->write($user);
			
			$this->_helper->getHelper('FlashMessenger')
				->addMessage('The profile is updated successfully');
			$this->_redirect($this->view->serverUrl() . $this->view->url(array('user_name' => $username), 'core_user_edit'));
			
		}
	}
}