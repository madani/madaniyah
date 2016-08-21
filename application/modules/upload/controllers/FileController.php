<?php

class Upload_FileController extends Zend_Controller_Action
{
	public function uploadAction()
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		
		$request = $this->getRequest();
		if (!$request->isPost()) {
			return;
		}
		
		/**
		 * Get Config
		 */
		$config = Pandamp_Module_Config::getConfig('upload');
		$tool = $config->thumbnail->tool;
		$size = $config->size->toArray();
		$sizes = array();
		foreach ($size as $key => $value) {
			list($method, $width, $height) = explode('_', $value);
			$sizes[$key] = array('method' => $method, 'width' => $width, 'height' => $height);
		}
		
		$user 	  = Zend_Auth::getInstance()->getIdentity();
		$userName = $user->user_name;
		$module   = $request->getPost('mod');
		$thumbnailSizes = $request->getPost('thumbnails', null);
		
		/**
		 * Prepare folders
		 */
		$dir  = dirname($_SERVER['SCRIPT_FILENAME']) . DS . 'fs';
		$path = implode(DS, array($module, $userName, date('Y'), date('m')));
		Pandamp_Utility_File::createDirs($dir, $path);
		
		/**
		 * Upload file
		 */
		$ext 	   = explode('.', $_FILES['Filedata']['name']);
		$extension = $ext[count($ext) - 1];
		$fileName  = uniqid();
		$file 	   = $dir . DS . $path . DS . $fileName . '.' . $extension;
		move_uploaded_file($_FILES['Filedata']['tmp_name'], $file);
		
		/**
		 * Generate thumbnails if requested
		 */
		if (!isset($thumbnailSizes) || $thumbnailSizes == null) {
			$thumbnailSizes = array_keys($sizes);
		} else if ($thumbnailSizes != 'none') {
			$thumbnailSizes = explode(',', $thumbnailSizes);
		}
		
		$service = null;
		switch (strtolower($tool)) {
			case 'imagemagick':
				//$service = new \Pandamp\Image\ImageMagick();
				break;
			case 'gd':
				$service = new Pandamp_Image_GD();
				break;
		}
		
		$ret = array();
		
		/**
		 * Remove script filename from base URL
		 */
		$baseUrl = $this->view->baseUrl();
		if (isset($_SERVER['SCRIPT_NAME']) && ($pos = strripos($baseUrl, basename($_SERVER['SCRIPT_NAME']))) !== false) {
			$baseUrl = substr($baseUrl, 0, $pos);
		}
		$prefixUrl 		 = $baseUrl . '/fs/' . $module . '/' . $userName . '/' . date('Y') . '/' . date('m');
		$ret['original'] = array(
				'url'  => $prefixUrl . '/' . $fileName . '.' . $extension,
				'size' => null,
		);
		
		if ($thumbnailSizes != 'none') {
			$service->setFile($file);
			$ret['original']['size'] = $service->getWidth() . ' x ' . $service->getHeight();
				
			foreach ($thumbnailSizes as $s) {
				$service->setFile($file);
				$method = $sizes[$s]['method'];
				$width 	= $sizes[$s]['width'];
				$height = $sizes[$s]['height'];
				
				$f 		 = $fileName . '_' . $s . '.' . $extension;
				$newFile = $dir . DS . $path . DS . $f;
				
				/**
				 * Create thumbnail
				 */
				switch ($method) {
					case 'resize':
						$service->resizeLimit($newFile, $width, $height);
						break;
					case 'crop':
						$service->crop($newFile, $width, $height);
						break;
				}
				
				$ret[$s] = array(
					'url'  => $prefixUrl . '/' . $f,
					'size' => $width . ' x ' . $height,
				);
			}
		}

		
		/**
		 * Return the reponse
		 */
		$ret = Zend_Json::encode($ret);
		$this->getResponse()->setBody($ret);
	}
	
	public function deleteAction()
	{
		$request = $this->getRequest();
		
		$images = $request->getPost('img');
		$db = $request->getPost('db');
		$id = $request->getPost('id');
		
		$images = json_decode($images, true);
				
		foreach ($images as $image) {
			$path = parse_url($image['url'], PHP_URL_PATH);
			
			$fullpath = dirname($_SERVER['SCRIPT_FILENAME']) . DS . $path;
			
			unlink($fullpath);
			
		}
		
		if (isset($db) && isset($id) && ($db==1)) {
			$postService = $this->getServiceLocator()->get('Application\Service\Post');
			$postService->deleteImage($id,$images);
		}
		
		return $this->response;
	}
	
	public function checkAction()
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		
		$request = $this->getRequest();
		
		$user 	  = Zend_Auth::getInstance()->getIdentity();
		$userName = $user->user_name;
		$module   = $request->getPost('mod');
		$fileName    = $request->getPost('filename');
		
		$dir  = dirname($_SERVER['SCRIPT_FILENAME']) . DS . 'fs';
		$path = implode(DS, array($module, $userName, date('Y'), date('m')));
		
		$ext 	   = explode('.', $fileName);
		$extension = $ext[count($ext) - 1];
		$file 	   = $dir . DS . $path . DS . $fileName . '.' . $extension;
		
		if (file_exists($file)) {
			echo 1;
		}
		else 
		{
			echo 0;
		}
	}
}