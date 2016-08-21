<?php
class Core_IndexController extends Zend_Controller_Action
{
	public function indexAction()
	{
		$config = Pandamp_Config::getConfig();
		
		$this->view->doctype(Zend_View_Helper_Doctype::XHTML1_RDFA);
		$this->view->headMeta()->setProperty('og:title', $config->web->title);
		$this->view->headTitle($config->web->title);
		$this->view->headTitle()->setSeparator(' - ');
	}
	
	public function sitemapAction()
	{
		
	}
	
	public function privacyAction()
	{
		
	}
	
	public function termsAction()
	{
		
	}
	
	public function aboutusAction()
	{
		
	}
	
	public function contactusAction()
	{
		
	}
}