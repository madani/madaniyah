<?php
use Pandamp\Utility\Ip,
	Pandamp\Utility\Formatting;
class Core_IndexController extends Zend_Controller_Action
{
	public function indexAction()
	{
		$config = Pandamp_Config::getConfig();
		
		$this->view->doctype(Zend_View_Helper_Doctype::XHTML1_RDFA);
		$this->view->headMeta()->setProperty('og:title', $config->web->title);
		$this->view->headTitle($config->web->title);
		$this->view->headTitle()->setSeparator(' - ');
	}
	
	public function sitemapAction()
	{
		
	}
	
	public function privacyAction()
	{
		$article = News_Models_Post::fetchOne(['type' => 'policy']);
		$title = strip_tags($article->title);
		
		$this->view->doctype(Zend_View_Helper_Doctype::XHTML1_RDFA);
		$this->view->headMeta()->setProperty('og:title', $title);
		
		$this->view->assign('article', $article);
	}
	
	public function termsAction()
	{
		$article = News_Models_Post::fetchOne(['type' => 'terms']);
		$title = strip_tags($article->title);
		
		$this->view->doctype(Zend_View_Helper_Doctype::XHTML1_RDFA);
		$this->view->headMeta()->setProperty('og:title', $title);
		
		$this->view->assign('article', $article);
	}
	
	public function aboutAction()
	{
		$article = News_Models_Post::fetchOne(['type' => 'about']);
		$title = strip_tags($article->title);
		
		$this->view->doctype(Zend_View_Helper_Doctype::XHTML1_RDFA);
		$this->view->headMeta()->setProperty('og:title', $title);
		
		$this->view->assign('article', $article);
	}
	
	public function contactAction()
	{
		$request = $this->getRequest();
		
		if ($request->isPost()) {
			$service = $request->getPost('mycontact_services');
			$name = $request->getPost('mycontact_name');
			$email = $request->getPost('mycontact_email');
			$body = $request->getPost('mycontact_body');
			$imnotarobot = $request->getPost('imnotarobot');
			
			if ($imnotarobot == 'on') {
				$now = new \MongoDate();
				$data = new News_Models_Post([
					'service' => $service,
					'type' => 'contact',
					'status' => 'publish',	
					'content' => (new Pandamp_Utility_Posts)->sanitize_post_content($body),
					'date' => [
						'create' => $now,
					],
					'name' => $name,
					'email' => $email,
					'ip' => (new Ip)->getHttpRealIp(),
				]);
				
				$auth = Zend_Auth::getInstance();
				if ($auth->hasIdentity())
				{
					$auth = $auth->getIdentity();
					$data->author = [
						'id' => (string) $auth['_id']->{'$id'},
						'name' => implode(' ', [$auth->first_name, $auth->last_name]),
						'username' => $auth->user_name
					];
				}
				
				unset($data->_type);
				
				News_Models_Post::insert($data->export());
			}
		}
	}
}