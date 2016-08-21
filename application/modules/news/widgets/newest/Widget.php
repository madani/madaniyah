<?php
use Pandamp\Utility\Formatting;
class News_Widgets_Newest_Widget extends Pandamp_Widget
{
	protected function _prepareShow() 
	{
		$limit = (int) $this->_request->getParam('limit');
		$page = $this->_request->getParam('page', 1);
		
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
		
		//$posts = News_Models_Post::all($criteria)->sort(['date.publish' => -1])->limit($limit)->skip(($page - 1) * $limit);
		$posts = News_Models_Post::all($criteria)->sort(['date.publish' => -1]);

		// find current index based on _id
		// $current = array_search('5537750cb6a97b8320b35d4a', array_keys($p));

		$sticky = false;
		$content = 0;
		$data = array();
		foreach ($posts as $post) {
			$data[$content][0] = $post['_id'];
			$data[$content][1] = $post['title'];
			$data[$content][2] = $post['slug'];
			$data[$content][3] = date('d/m/Y H:i:s', $post['date']['publish']->sec);
			$data[$content][4] = (new formatting)->teaser(30, $post['content']);
			$data[$content][5] = $post['fields']['images'][0]['square']['url'];
			
			if (isset($post['sticky'])) {
				$sticky = true;
				$data[$content][6] = $post['sticky']-1;
				// current index position
				$data[$content][7] = $content;
			}
			
			$content++;
		}
		
		if ($sticky) {
			foreach ($data as $key => $val) {
				if (isset($val[6])) {
					// if sticky=current index then ignore no need to rearrange
					if ($val[6]==$val[7]) continue;
					$this->moveElement($data, $val[7], $val[6]);
				}
			}
			
		}
		
		//$adapter = new Shanty_Paginator_Adapter_Mongo($posts);
		$adapter = new Zend_Paginator_Adapter_Array($data);
		$paginator = new Zend_Paginator($adapter);
		$paginator->setCurrentPageNumber($page);
		$paginator->setItemCountPerPage($limit);
		
		$this->_view->assign('posts',$paginator);
		$this->_view->assign('page', $page);
		$this->_view->assign('formatting', new Formatting());
	}
	
	function moveElement(&$array, $a, $b) {
		$p1 = array_splice($array, $a, 1);
		$p2 = array_splice($array, 0, $b);
		$array = array_merge($p2,$p1,$array);
	}
}
