<?php
class Pandamp_Autoloader extends Zend_Loader_Autoloader_Resource
{
    public function __construct($options)
    {
        parent::__construct($options);
    }
    
	public function autoload($class)
    {
    	$prefix = APPLICATION_PATH . DS;
    	$paths  = explode('_', $class);
    	switch (strtolower($paths[0])) {
    		case 'plugins':
    		case 'hooks':
    			$prefix .= '';
    			break;
    		default:
    			$prefix .= 'modules' . DS;
    			break;
    	}
    	    	
		$className = $paths[count($paths) - 1];
		$classFile = substr($class, 0, -strlen($className));
		$classFile = $prefix . strtolower(str_replace('_', DS, $classFile)) . $className . '.php';
		
		if (file_exists($classFile)) {
			return require_once $classFile;
		}
    	
        return false;
    }
}
