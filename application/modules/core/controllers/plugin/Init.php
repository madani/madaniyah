<?php
class Core_Controllers_Plugin_Init extends Zend_Controller_Plugin_Abstract
{
	public function preDispatch(Zend_Controller_Request_Abstract $request)
	{
		$config = Pandamp_Config::getConfig();
		
		$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
		if (null === $viewRenderer->view) {
			$viewRenderer->initView();
		}
		$view = $viewRenderer->view;
		//$view->doctype('XHTML1_STRICT');
		$view->doctype('XHTML1_TRANSITIONAL');
		//$view->setEncoding('UTF-8');
		$view->addHelperPath(LIB_DIR . DS . 'Pandamp' . DS . 'View' . DS . 'Helper', 'Pandamp_View_Helper');
		$view->addHelperPath(APPLICATION_PATH . DS . 'modules' . DS . 'core' . DS . 'views' . DS . 'helpers', 'Core_View_Helper');
		
		/**
		 * Set base URL
		 */
		$view->getHelper('BaseUrl')->setBaseUrl($config->web->url->base);
		
		/**
		 * Append meta tags
		 */
		$view->headMeta()->appendName('description', $config->web->meta->description);
		$view->headMeta()->appendName('keywords', $config->web->meta->keyword);
		
		/**
		 * Set theme for site
		 * User can change skin at real time
		 * Check whether user set skin cookie or not
		 */
		$skin = (isset($_COOKIE['APP_SKIN'])) ? $_COOKIE['APP_SKIN'] : $config->web->skin;
		$view->assign('APP_SKIN', $skin);
		
		$template = (!Zend_Registry::isRegistered(Pandamp_Keys::APP_TEMPLATE)
				|| Zend_Registry::get(Pandamp_Keys::APP_TEMPLATE) == null
				|| Zend_Registry::get(Pandamp_Keys::APP_TEMPLATE) == '')
				? $config->web->template : Zend_Registry::get(Pandamp_Keys::APP_TEMPLATE);
		Zend_Registry::set(Pandamp_Keys::APP_TEMPLATE, $template);
		$view->assign('APP_TEMPLATE', $template);
		
		$view->assign('APP_URL', $config->web->url->base);
		$view->assign('APP_STATIC_SERVER', $config->web->url->static);
		$view->assign('SITE_NAME', $config->web->name);
		
		/**
		 * Get charset from configuration file
		 */
		$charset = $config->web->charset;
		if (null == $charset) {
			$charset = 'utf-8';
		}
		$view->assign('CHARSET', $charset);
		
		/**
		 * Set layout
		*/
		Zend_Layout::startMvc(array('layoutPath' => APPLICATION_PATH . DS . 'templates' . DS . $template . DS . 'layouts'));
		Zend_Layout::getMvcInstance()->setLayout('default');
	}
}