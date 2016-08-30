<?php
use Pandamp\Utility\Formatting;
class Category_CategoryController extends Zend_Controller_Action
{
	public function addAction()
	{
		$request = $this->getRequest();
		$node = $request->getParam('node', 'root');
		if ($request->isPost()) {
			$title = $request->getPost('name');
			$slug = $request->getPost('slug');
			$node = $request->getPost('node');
			$category = [
				'_id' => uniqid('mp'),
				'name' => $title,
				'slug' => $slug,
				'created_date' => new \MongoDate(),
				'user_id' => Zend_Auth::getInstance()->getIdentity()->user_name,
						
			];
			unset($category['_type']);
			Category_Models_Category::insert($category);
			if ($node == "root") {
				$category['parent'] = $category['_id'];
			}
			else
			{
				$c = (new Category_Models_Category)->findById($node);
				$category['path'] = $c->path.$c->_id.'/';
				$category['parent'] = $node;
			}
			Category_Models_Category::update(['_id'=>$category['_id']], $category);
		}
		$this->view->assign('currentNode',$node);
	}
	
	public function listAction()
	{
		$request = $this->getRequest();
		$limit = $this->getParam('limit',15);
		$page = $this->getParam('page',1);
		$slug = $this->getParam('slug');
		
		$criteria = [];
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
			]
		];
		
		if (isset($slug) && !empty($slug)) {
			$cat = [
				'fields.category.slug' => $slug
			];
			$criteria = array_merge($criteria,$cat);
		}
		
		$posts = News_Models_Post::all($criteria)->sort(['date.publish' => -1]);
		$adapter = new Shanty_Paginator_Adapter_Mongo($posts);
		$paginator = new Zend_Paginator($adapter);
		$paginator->setCurrentPageNumber($page);
		$paginator->setItemCountPerPage($limit);
		
		$this->view->assign('posts',$paginator);
		$this->view->assign('page', $page);
		$this->view->assign('catName', ucwords(strtolower(str_replace('-', ' ', $slug))));
		$this->view->assign('formatting', new Formatting());
	}
}