<?php
/**
 * @author	2011-2012 Nihki Prihadi <nihki@madaniyah.com>
 * @version $Id: FlashMessenger.php 1 2011-12-24 12:32Z $
 */

class Core_View_Helper_FlashMessenger extends Zend_View_Helper_Abstract 
{
	public function flashMessenger()
	{
		$this->view->addScriptPath(Zend_Layout::getMvcInstance()->getLayoutPath());
		$this->view->addScriptPath(APPLICATION_PATH . DS . 'modules' . DS . 'core' . DS . 'views' . DS . 'scripts');
		
		$flashMsgHelper = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger');
		$this->view->assign('messages', $flashMsgHelper->getMessages());
		
		return $this->view->render('_partial/_messages.phtml');
	}
}