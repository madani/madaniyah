<?php
use Pandamp\Utility\Formatting;
class Tag_TagController extends Zend_Controller_Action
{
	public function listAction()
	{
		$request = $this->getRequest();
		
		$perPage  = 20;
		$page = $request->getParam('page', 1);
		
		$tags = Tag_Models_Tag::all()->sort(['created_date' => -1])->limit($perPage)->skip(($page - 1) * $perPage);
		$adapter = new Shanty_Paginator_Adapter_Mongo($tags);
		$paginator = new Zend_Paginator($adapter);
		$paginator->setCurrentPageNumber($page);
		$paginator->setItemCountPerPage($perPage);
		
		$this->view->assign('tags',$paginator);
		
	}
	
	public function slugAction()
	{
		$request = $this->getRequest();
		$perPage  = 15;
		$page = $request->getParam('page', 1);
		$alpha = $request->getParam('alpha');
		$tagslug = $request->getParam('slug');
		
		$tagTitle = ucwords(strtolower(str_replace('-', ' ', $tagslug)));
		
		$criteria = [
		'$and'=>[
		['$or' => [
		['date.publish' => ['$exists' => false]],
		['date.publish' => null],
		['date.publish' => '0000-00-00 00:00:00'],
				['date.publish' => ['$lte' => new \MongoDate()]]
				]],
				['$or' => [
				['date.expire' => ['$exists' => false]],
						['date.expire' => null],
						['date.expire' => '0000-00-00 00:00:00'],
    			['date.expire' => ['$gte' => new \MongoDate()]]
		    					]]
		    							],'fields.tags' => ['$in' => [$tagTitle]]
		    			];
		
		$tagService = (new Tag_Models_Tag)->increaseViews($tagslug);
		
		$posts = News_Models_Post::all($criteria)->sort(['date.publish' => -1])->limit($perPage)->skip(($page - 1) * $perPage);
		$adapter = new Shanty_Paginator_Adapter_Mongo($posts);
		$paginator = new Zend_Paginator($adapter);
		$paginator->setCurrentPageNumber($page);
		$paginator->setItemCountPerPage($perPage);
		
		$this->view->assign('posts',$paginator);
		$this->view->assign('tagtitle',$tagTitle);
		$this->view->assign('alphabet',$alpha);
		$this->view->assign('tagslug',$tagslug);
		$this->view->assign('formatting', new Formatting());
	}
	
	public function alphaAction()
	{
		$request = $this->getRequest();
		$perPage  = 15;
		$page = $request->getParam('page', 1);
		$alpha = $request->getParam('alpha');
		
		$criteria = [
			'tag_text' => new \MongoRegex("/^".$alpha."/i")
		];
		
		$tags = Tag_Models_Tag::all($criteria)->sort(['created_date' => -1])->limit($perPage)->skip(($page - 1) * $perPage);
		$adapter = new Shanty_Paginator_Adapter_Mongo($tags);
		$paginator = new Zend_Paginator($adapter);
		$paginator->setCurrentPageNumber($page);
		$paginator->setItemCountPerPage($perPage);
		
		$this->view->assign('tags',$paginator);
		$this->view->assign('alphabet',$alpha);
	}
}