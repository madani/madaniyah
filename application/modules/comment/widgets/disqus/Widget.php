<?php

class Comment_Widgets_Disqus_Widget extends Pandamp_Widget 
{
	protected function _prepareShow() 
	{
		$username = $this->_request->getParam('username');
		$this->_view->assign('username', $username);
	}
}
