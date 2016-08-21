<?php
use Pandamp\Utility\Formatting;
class News_Widgets_CatalogByUser_Widget extends Pandamp_Widget
{
	protected function _prepareShow()
	{
		$limit = (int) $this->_request->getParam('limit');
		$page = $this->_request->getParam('page', 1);
		
		$username = Zend_Controller_Front::getInstance()->getRequest()->getParam('user_name'); 
		
		$posts = News_Models_Post::all(['author.username'=>$username])->sort(['date.publish'=>-1]);
		$adapter = new Shanty_Paginator_Adapter_Mongo($posts);
		$paginator = new Zend_Paginator($adapter);
		$paginator->setCurrentPageNumber($page);
		$paginator->setItemCountPerPage($limit);
		
		$this->_view->assign('posts',$paginator);
		$this->_view->assign('page', $page);
		$this->_view->assign('formatting', new Formatting());
	}
}