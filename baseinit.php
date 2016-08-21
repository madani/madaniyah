<?php
/**
 * @author	2011-2018 Nihki Prihadi <nihki@madaniyah.com>
 * @version $Id: baseinit.php 1 2013-03-11 09:41
 */

// error_reporting(E_ALL|E_STRICT);

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/application'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

    
define('DS', DIRECTORY_SEPARATOR);    
define('PS', PATH_SEPARATOR);

define('APPLICATION_CONFIG_FILENAME', 'application.ini');
define('CONFIG_PATH' , APPLICATION_PATH . '/configs') ;
define('ROOT_DIR' , realpath(dirname(__FILE__))) ;
define('APP_DIR', ROOT_DIR . DS . 'application');
define('LIB_DIR', ROOT_DIR . DS . 'library');
define('TEMP_DIR', ROOT_DIR . DS . 'temp');

    
// Ensure library/ is on include_path
set_include_path(implode(PS, array(
realpath(APPLICATION_PATH . '/../library'), PS,
get_include_path(),
)));

setlocale (LC_TIME, 'id_ID');

/** Zend_Application */
require_once 'Zend/Application.php';
require_once 'Pandamp/ZP.php';

// Create application, bootstrap, and run
$application = new Pandamp_ZP(
    APPLICATION_ENV,
    array(
    	'configFile' => CONFIG_PATH . DS . APPLICATION_CONFIG_FILENAME
    )
);

$registry = Zend_Registry::getInstance();
$registry->set(Pandamp_Keys::REGISTRY_APP_OBJECT, $application);

$registry->set('files', $_FILES);