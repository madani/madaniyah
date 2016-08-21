<?php
class News_PraytimeController extends Zend_Controller_Action
{
	public function hijmaAction()
	{
		$this->_helper->layout()->disableLayout();
		
		$request = $this->getRequest();
		$city = $request->getParam('city',310);
		
		$this->view->assign('city',$city);
	}
}