<?php
/**
 * @author	2011-2012 Nihki Prihadi <nihki@madaniyah.com>
 * @version $Id: Bootstrap.php 1 2012-02-07 13:30:12Z $
 */

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	/**
	 * @var Zend_log
	 */
	protected $_logger;
	
	/**
	 * The Zend_Log class defines the following priorities:
	 * 1. EMERG   = 0;  // Emergency: system is unusable
	 * 2. ALERT   = 1;  // Alert: action must be taken immediately
	 * 3. CRIT    = 2;  // Critical: critical conditions
	 * 4. ERR     = 3;  // Error: error conditions
	 * 5. WARN    = 4;  // Warning: warning conditions
	 * 6. NOTICE  = 5;  // Notice: normal but significant condition
	 * 7. INFO    = 6;  // Informational: informational messages
	 * 8. DEBUG   = 7;  // Debug: debug messages
	 *
	 * used $log->info, $log->err, etc.
	 *
	 * @sample:
	 * $log = Zend_Registry::get('Zend_Log');
	 * $log->err('test log');
	 */
	protected function _initRegisterLogger()
	{
		$this->bootstrap('Log');
	
		if (!$this->hasPluginResource('Log')) {
			//throw new Zend_Exception('Log not enabled in application.ini');
			throw new Pandamp_Exception_NotFound();
		}
	
		$logger = $this->getResource('Log');
		assert($logger != NULL);
		$this->_logger = $logger;
		Zend_Registry::set('Zend_Log',$logger);
	}
	
	/**
	 * Configure the pluginloader cache
	 */
	protected function _initPluginLoaderCache()
	{
		if ('production' == $this->getEnvironment()) {
			$classFileInCache = APPLICATION_PATH . '/../temp/cache/pluginLoaderCache.php';
			if (file_exists($classFileInCache))
			{
				include_once $classFileInCache;
			}
			Zend_Loader_PluginLoader::setIncludeFileCache($classFileInCache);
		}
	}
	
	protected function _initAutoload()
	{
		$autoloader = Zend_Loader_Autoloader::getInstance();
		
		$modules = Pandamp_Module_Loader::getInstance()->getModuleNames();
		new Pandamp_Autoloader(array(
			'basePath'  => APPLICATION_PATH,
			'namespace' => 'Plugins_',
		));
		
		foreach ($modules as $module) {
			new Pandamp_Autoloader(array(
				'basePath'  => APPLICATION_PATH . DS . 'modules' . DS . $module,
				'namespace' => ucfirst($module) . '_',
			));
		}
		
		require_once 'htmlpurifier/HTMLPurifier/Bootstrap.php';
		HTMLPurifier_Bootstrap::registerAutoload();
		
		return $autoloader;		
	}
	
	/**
	 * Init routes
	 * @return void
	 */
	protected function _initRoutes()
	{
		$this->bootstrap('FrontController');
		$front = $this->getResource('FrontController');
	
		$routes = Pandamp_Module_Loader::getInstance()->getRoutes();
		$front->setRouter($routes);
	
		/**
		 * Don't use default route
		 */
		$front->getRouter()->removeDefaultRoutes();
	}
	
	/**
	 * Add action helpers
	 *
	 * @return void
	 */
	protected function _initActionHelpers()
	{
		Zend_Controller_Action_HelperBroker::addHelper(new Pandamp_Controller_Action_Helper_RegistryAccess());
		Zend_Controller_Action_HelperBroker::addPath(LIB_DIR . DS . 'Pandamp' . DS . 'Controller' . DS . 'Action' . DS . 'Helper',
		'Pandamp_Controller_Action_Helper');
	}
	
	/*protected function _initMongodb()
	{
		$mongodb = $this->getOption('mongodb');
		$connections = [
			'master' => [
				'host' => $mongodb['host'],
				'port' => $mongodb['port'],
				'db' => $mongodb['db']
			]
		];
		if (!empty($mongodb['username']) && !empty($mongodb['password']))
		{
			$connections['master']['username'] = $mongodb['username'];
			$connections['master']['password'] = $mongodb['password'];
		}
		Shanty_Mongo::addConnections($connections);
	}*/
	
	/**
	 * Register plugins
	 * @return void
	 */
	protected function _initPlugins()
	{
		$this->bootstrap('FrontController');
		$front = $this->getResource('FrontController');
	
		/**
		 * Register plugins
		 * The alternative way is that put plugin to /application/configs/application.ini:
		 * resources.frontController.plugins.pluginName = "Plugin_Class"
		 */
		$front->registerPlugin(new Core_Controllers_Plugin_Init())
			  ->registerPlugin(new Core_Controllers_Plugin_Auth());
	
		/**
		 * Error handler
		 * @todo set throwexceptions=false in development section inside application.ini
		 */
		$front->registerPlugin(new Zend_Controller_Plugin_ErrorHandler(array(
			'module' 	 => 'core',
			'controller' => 'error',
			'action'     => 'error',
		)));
	}
}