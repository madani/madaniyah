<?php
class Core_ErrorController extends Zend_Controller_Action
{
	/**
	 * Show error.
	 * If you want to throw data not found exception, add the following code to your controller action:
	 * <code>
	 * 	if (null == $data) {
	 * 		throw new Pandamp_Exception_NotFound();
	 * 	}
	 * </code>
	 *
	 * @return void
	 */
	public function errorAction()
	{
		Zend_Layout::getMvcInstance()
		->setLayoutPath(APPLICATION_PATH . DS . 'templates' . DS . $this->view->APP_TEMPLATE . DS . 'layouts')
		->setLayout('error');
		//die('f');
	}
}