<?php
class News_Widgets_Category_Widget extends Pandamp_Widget
{
	protected function _prepareShow() 
	{
		$this->_view->assign('slug', Zend_Controller_Front::getInstance()->getRequest()->getParam('slug'));
	}
}
