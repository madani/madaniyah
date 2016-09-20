<?php
class News_View_Helper_Category extends Zend_View_Helper_Abstract
{
	public function category($slug=null, $isCount=FALSE)
	{
		$category = $this->_traverseFolder('root','',0,$slug,$isCount);
		
		return $category;
	}
	
	protected function _traverseFolder($folderGuid, $sGuid, $level, $slug, $isCount=FALSE)
	{
		$rowSet = (new Category_Models_Category)->fetchChildren($folderGuid);
		$sGuid = '';
		foreach($rowSet as $row)
		{
			$ss = ($slug != $row['slug']) ? '' : ' class="active"';
			if (!$isCount)
				$option = '<li'.$ss.'><a href="'.$this->view->url(['slug'=>$row['slug']],'category_category_detail').'">'.str_repeat("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;", $level).$row['name'].'</a></li>';
			else 
				if (self::getCountPost($row['_id'])) {
					$badge = ($isCount) ? ' <span class="badge">'.self::getCountPost($row['_id']).'</span>' : '';
					$option = '<li'.$ss.'><a href="'.$this->view->url(['slug'=>$row['slug']],'category_category_detail').'">'.$row['name'].$badge.'</a></li>';
				}
				else
					$option = '';
			
			$sGuid .= $option . $this->_traverseFolder($row['_id'], '', $level+1, $slug, $isCount);
		}
			
		return $sGuid;
	}
	
	protected function getCountPost($id)
	{
		return News_Models_Post::all([
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
				    			],
					'fields.category.categoryId' => $id
				])->count();
	}
}