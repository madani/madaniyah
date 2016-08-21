<?php
/**
 * @author	2011-2012 Nihki Prihadi
 * @version $Id: Cdn.php 1 2012-02-07 16:46Z $
 */

class Core_View_Helper_Cdn extends Zend_View_Helper_Abstract 
{
    static $_types = array(
        'default' 	=> '',
        'images'  	=> 'http://static.hukumonline.dev/frontend/default/images',                
        'styles'  	=> 'http://static.hukumonline.dev/frontend/default/css',
        'columnal'  => 'http://static.hukumonline.dev/frontend/default/columnal-0.85',
        '960'  		=> 'http://static.hukumonline.dev/frontend/default/960',
        'skins'		=> 'http://np.local/skins',
        'scripts' 	=> 'http://js.hukumonline.dev',
        'rim' 	  	=> 'http://images.hukumonline.dev'
    );
    
    static function setTypes($types)        
    {
		self::$_types = $types;
    }
    
    public function cdn($type = 'default')        
    {
        if (!isset(self::$_types[$type])) {
			throw new Exception('No CDN set for resource type ' . $type);
        }
        return self::$_types[$type];
    }
	
}
