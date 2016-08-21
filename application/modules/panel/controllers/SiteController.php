<?php
class Panel_SiteController extends Zend_Controller_Action
{
	public function indexAction()
	{
		Zend_Layout::getMvcInstance()->setLayout('ecommerce');
		
		$article = News_Models_Post::fetchOne(['type' => 'site','slug' => $sitename]);
		
		if (null == $article) {
			throw new Pandamp_Exception_NotFound();
		}
	}
	
	public function addAction()
	{
		$this->_helper->viewRenderer->setNoRender();
		
		$request = $this->getRequest();
		$sitename = $request->getPost('sitename');
		$auth = Zend_Auth::getInstance()->getIdentity();
		$now = new \MongoDate();
		$data = new News_Models_Post([
				'author' => [
					'id' => (string) $auth['_id']->{'$id'},
					'name' => implode(' ', [$auth->first_name,$auth->last_name]),
					'username' => $auth->user_name,
				],
				'slug' => (new Pandamp_Utility_Posts)->sanitize_post_name($sitename),
				'title' => (new Pandamp_Utility_Posts)->sanitize_post_title($sitename),
				'status' => 'online',
				'date' => [
					'create' => $now,
					'update' => $now,
				]
			]);
		
		unset($data->_type);
		
		News_Models_Post::insert($data->export());
		
		$this->_helper->getHelper('FlashMessenger')
			->addMessage('New site has been added successfully');
		$this->_redirect($this->view->serverUrl() .  $this->view->url(array('slug' => $data->slug), 'panel_site_index'));
	}
	
	public function checkAction()
	{
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender(TRUE);
		
		$response = true;
		
		$request = $this->getRequest();
		if ($request->isPost()) {
			$sitename = $request->getPost('sitename');
			$article = News_Models_Post::fetchOne(['type' => 'site','title' => $sitename]);
			if ($article) 
				$response = false;
		}
		
		$this->getResponse()->setBody(Zend_Json::encode(['response'=>$response]));
	}
}