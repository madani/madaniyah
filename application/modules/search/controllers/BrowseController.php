<?php
use Pandamp\Utility\Formatting;
class Search_BrowseController extends Zend_Controller_Action
{
	public function resultAction()
	{
		$request = $this->getRequest();
		
		$perPage = 15;
		
		$page = $request->getParam('page',1);
		$keyword = $request->getParam('q');
		
		if( isset($keyword) && $keyword != '' ) {
			$criteria['$or'] = [
				['title' => new \MongoRegex("/".$keyword."/i")],
				['content' => new \MongoRegex("/".$keyword."/i")],
				['author.name' => new \MongoRegex("/".$keyword."/i")],
				['author.username' => new \MongoRegex("/".$keyword."/i")],
				['fields.category' => new \MongoRegex("/".$keyword."/i")],
			];
			$criteria['$and'] = [
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
			];
			$criteria['status'] = 'publish';
			$posts = News_Models_Post::all($criteria)->sort(['date.publish' => -1]);
			$adapter = new Shanty_Paginator_Adapter_Mongo($posts);
			$paginator = new Zend_Paginator($adapter);
			$paginator->setCurrentPageNumber($page);
			$paginator->setItemCountPerPage($perPage);
			
			$this->_helper->layout()->searchQuery = $keyword;
				
			$this->view->assign('posts',$paginator);
			$this->view->assign('page', $page);
			$this->view->assign('keyword', $keyword);
			$this->view->assign('formatting', new Formatting());
		}
	}
}