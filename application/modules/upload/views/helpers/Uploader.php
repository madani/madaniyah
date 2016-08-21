<?php
class Upload_View_Helper_Uploader extends Zend_View_Helper_Abstract
{
	/**
	 * @var int
	 */
	private $_counter = 0;
	
	public function uploader($module,
			$options = array('multi' => true, 'auto' => true, 'removeCompleted' => true, 'queueSizeLimit' => 0, 'fileSizeLimit' => 5242880, 'thumbnails' => null),
			$jsHandlers = array('onError' => null, 'onCancel' => null, 'onClearQueue' => null, 'onProgress' => null, 'onUploadComplete' => null),
			$uploadElementId = null)
	{
		$this->_counter++;
		
		if (null == $uploadElementId) {
			$uploadElementId = 'uploadFile_'.$this->_counter;
		}
		
		$this->view->assign('uploadElementId', $uploadElementId);
		$this->view->assign('module', $module);
		$this->view->assign('options', $options);
		$this->view->assign('handlers', $jsHandlers);
		$this->view->assign('sessionId', Zend_Session::getId());
		
		return $this->view->render('_partial/_uploader.phtml');
	}
}