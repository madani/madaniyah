<?php
class News_View_Helper_Category extends Zend_View_Helper_Abstract
{
	public function category($slug=null)
	{
		$category = $this->_traverseFolder('root','',0,$slug);
		
		return $category;
	}
	
	protected function _traverseFolder($folderGuid, $sGuid, $level, $slug)
	{
		$rowSet = (new Category_Models_Category)->fetchChildren($folderGuid);
		$sGuid = '';
		foreach($rowSet as $row)
		{
			$ss = ($slug != $row['slug']) ? '' : ' class="active"';
			if (self::getCountPost($row['_id'])) {
				$option = '<li'.$ss.'><a href="'.$this->view->url(['slug'=>$row['slug']],'category_category_detail').'">'.$row['name'].' <span class="badge">'.self::getCountPost($row['_id']).'</span></a></li>';
			}
			else
				$option = '';
			
			$sGuid .= $option . $this->_traverseFolder($row['_id'], '', $level+1, $slug);
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