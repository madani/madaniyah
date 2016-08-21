<?php
class News_View_Helper_Gdocnp extends Zend_View_Helper_Abstract
{
	public function gdocnp($id)
	{
		/**
		 * @todo
		 * query untuk previous document
		 * db.posts.find({_id:{'$lt':ObjectId("551b2a59b6a97bc115a0ba7a")}}).sort({"date.create":1}).limit(1).pretty()
		 * query untuk next document
		 * beda querynya terletak pada sort
		 * db.posts.find({_id:{'$lt':ObjectId("551b2a59b6a97bc115a0ba7a")}}).sort({"date.create":-1}).limit(1).pretty()
		 */
		
		$criteria = [
			'type'=>'article',
			'status'=>'publish',
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
		
// 		$left = array_merge($criteria,['_id' => ['$gt' => new \MongoId($id)]]);
// 		$left = News_Models_Post::all($left)->sort(['date.publish' => 1])->limit(1)->skip(0)->getNext();
		
// 		$left1 = array_merge($criteria,['_id' => ['$lt' => new \MongoId($id)]]);
		$left = News_Models_Post::all(['date.publish' => ['$lt' => new \MongoDate(strtotime($id))],'type'=>'article','status'=>'publish'],['title'=>1,'slug'=>1])->sort(['date.publish' => -1])->limit(1)->skip(0)->getNext();
		$left1 = News_Models_Post::all(['type'=>'article','status'=>'publish'],['title'=>1,'slug'=>1])->sort(['date.publish' => -1])->limit(1)->skip(0)->getNext();
		$right = News_Models_Post::all(['date.publish' => ['$gt' => new \MongoDate(strtotime($id))],'type'=>'article','status'=>'publish'],['title'=>1,'slug'=>1])->sort(['date.publish' => 1])->limit(1)->skip(0)->getNext();
		$right1 = News_Models_Post::all(['type'=>'article','status'=>'publish'],['title'=>1,'slug'=>1])->sort(['date.publish' => 1])->limit(1)->skip(0)->getNext();
		
// 		$right = array_merge($criteria,['_id' => ['$lt' => new \MongoId($id)]]);
// 		$right = News_Models_Post::all($right)->sort(['date.publish' => -1])->limit(1)->skip(0)->getNext();
		
// 		$right1 = array_merge($criteria,['_id' => ['$gt' => new \MongoId($id)]]);
// 		$right1 = News_Models_Post::all($right1)->sort(['date.publish' => -1])->limit(1)->skip(0)->getNext();
		
		$this->view->assign('leftTitle', (isset($left->title))?$left->title:$left1->title);
		$this->view->assign('leftSlug', (isset($left->slug))?$left->slug:$left1->slug);
		$this->view->assign('rightTitle', (isset($right->title))?$right->title:$right1->title);
		$this->view->assign('rightSlug', (isset($right->slug))?$right->slug:$right1->slug);
		
		return $this->view->render('_partial/gdocnp.phtml');
	}
}