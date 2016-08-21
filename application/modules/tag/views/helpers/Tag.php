<?php
class Tag_View_Helper_Tag extends Zend_View_Helper_Abstract
{
	public function tag($tagTitle)
	{
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
		
		$posts = News_Models_Post::all($criteria)->sort(['date.publish' => -1])->limit(5);
		
		$this->view->assign('posts',$posts);
		
		return $this->view->render('_partial/tag.phtml');
	}
}