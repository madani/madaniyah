<?php
class Core_Widgets_Sidprofile_Widget extends Pandamp_Widget
{
	protected function _prepareShow()
	{
		$username = Zend_Controller_Front::getInstance()->getRequest()->getParam('user_name');
		
		$userData = Core_Models_User::fetchOne(['user_name' => $username]);
		
		$this->_view->assign('user',$userData);
	}
}