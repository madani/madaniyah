<?php
class Pandamp_Module_Loader
{
	/**
	 * @var Pandamp_Module_Loader
	 */
	private static $_instance;
	
	/**
	 * @var array
	 */
	private $_moduleNames;
	
	/**
	 * @return Pandamp_Module_Loader
	 */
	public static function getInstance() 
	{
		if (null == self::$_instance) {
			self::$_instance = new self();
		}
		return self::$_instance;		
	}
	
	private function __construct() 
	{
		$this->_moduleNames = $this->_getModules();
	}
	
	public function getModuleNames() 
	{
		return $this->_moduleNames;
	}
	
	/**
	 * @return Zend_Controller_Router_Interface
	 * Add disable modules feature for switch between template
	 */
	public function getRoutes() 
	{
		if (null == $this->_moduleNames) {
			return;
		}
		$router = new Zend_Controller_Router_Rewrite();
		
		foreach ($this->_moduleNames as $name) {
			$configFiles = $this->_loadRouteConfigs($name);
			
			foreach ($configFiles as $file) {
				$config = new Zend_Config_Ini($file, 'routes');
				$router->addConfig($config, 'routes');
			}
		}
		
		return $router;
	}
	
	/**
	 * @return array
	 */
	private function _getModules() 
	{
		return Pandamp_Utility_File::getSubDir(APPLICATION_PATH . DS . 'modules');
	}
	
	/**
	 * @return array
	 */
	private function _loadRouteConfigs($moduleName) 
	{
		$dir = APPLICATION_PATH . DS . 'modules' . DS . $moduleName . DS . 'config' . DS . 'routes';
		if (!is_dir($dir)) {
			return array();
		}
		
		$configFiles = array();
		
		$dirIterator = new DirectoryIterator($dir);
		foreach ($dirIterator as $file) {
            if ($file->isDot() || $file->isDir()) {
                continue;
            }
            $name = $file->getFilename();
            if (preg_match('/^[^a-z]/i', $name) || ('CVS' == $name) 
            		|| ('.svn' == strtolower($name))) {
                continue;
            }
            $configFiles[] = $dir . DS . $name;
        }
		
		return $configFiles;
	}		
}