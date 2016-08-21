<?php
/**
 * @author	2011-2018 Nihki Prihadi
 * @version $Id: User.php 1 2013-05-16 16:33Z $
 */

class Pandamp_Controller_Router_Route_User extends Zend_Controller_Router_Route
{
	public static function getInstance(Zend_Config $config)
	{
		$defs = ($config->defaults instanceof Zend_Config) ? $config->defaults->toArray() : array();
		return new self($config->route, $defs);
	}
	
	public function __construct($route, $defaults = array())
	{
		$this->_route = trim($route, $this->_urlDelimiter);
		$this->_defaults = (array)$defaults;
	}
	
	public function match($path,$partial=false)
	{
		if ($path instanceof Zend_Controller_Request_Http) {
			$path = $path->getPathInfo();
		}
		
		$path = trim($path, $this->_urlDelimiter);
		$pathBits = explode($this->_urlDelimiter, $path);
		
		if (count($pathBits) != 1) {
			return false;
		}
		
		// check database for this user
		$result = Core_Models_User::fetchOne(['user_name' => $pathBits[0]],['user_name'=>1]);
		if ($result) {
			// user found
			$values = $this->_defaults + $result->export();
			
			return $values;
		}
		
		return false;
	}
	
	public function assemble($data = array(), $reset = false, $encode = false)
	{
		return $data['username'];
	}
}