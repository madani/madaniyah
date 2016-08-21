<?php 
class Category_View_Helper_Breadcrumb extends Zend_View_Helper_Abstract
{
	public function breadcrumb($categoryId = 'root')
	{
		$aPath = [];
		
		$browseUrl = $this->view->url([],'category_category_list');
		
		if ($categoryId == 'root')
		{
			$aPath[0]['title'] = 'Root';
			$aPath[0]['url'] = $browseUrl;
		}
		else
		{
			$category = (new Category_Models_Category)->findById($categoryId);
			if (!empty($category['path']))
			{
				$exp = explode("/", $category['path']);
				$sPath = 'root >';
				$aPath[0]['title'] = 'Root';
				$aPath[0]['url'] = $browseUrl;
				$i = 1;
				if(count($exp))
				{
					$sPath1 = '';
					foreach ($exp as $guid)
					{
						if(!empty($guid))
						{
							$rowFolder1 = (new Category_Models_Category)->findById($guid);
							$sPath1 .= $rowFolder1['name'] . ' > ';
							$aPath[$i]['title'] = $rowFolder1['name'];
							$aPath[$i]['url'] = $this->view->url(['slug'=>$rowFolder1['slug']],'category_category_detail');
							$i++;
						}
					}
						
					$aPath[$i]['title'] = $category['name'];
					$aPath[$i]['url'] = $this->view->url(['slug'=>$category['slug']],'category_category_detail');
				}
			}
			else
			{
				$aPath[0]['title'] = 'Root';
				$aPath[0]['url'] = $browseUrl;
				$aPath[1]['title'] = $category['name'];
				$aPath[1]['url'] = $this->view->url(['slug'=>$category['slug']],'category_category_detail');
			}
		}
		
		$this->view->assign('aPath',$aPath);
		
		return $this->view->render('_partial/breadcrumb.phtml');
	}
}