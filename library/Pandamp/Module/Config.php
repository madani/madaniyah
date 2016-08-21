<?php
class Pandamp_Module_Config 
{
	public static function getConfig($module) 
	{
		$file = APPLICATION_PATH . DS . 'modules' . DS . $module . DS . 'config' . DS . 'config.ini';
		if (!file_exists($file)) {
			return null;
		}
		$file = new Zend_Config_Ini($file);
		return $file;
	}
}
