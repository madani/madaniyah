<?php
/**
 * @author	2011-2012 Nihki Prihadi <nihki@madaniyah.com>
 * @version $Id: Widget.php 1 2012-01-13 16:20Z $
 */

class Pandamp_View_Helper_Widget extends Zend_View_Helper_Abstract 
{
	public function __construct()
	{}
	
	public function widget($module, $name, array $params = array()) 
	{
		$module  = strtolower($module);
		$name 	 = strtolower($name);
		$timeout = isset($params[Pandamp_Widget::PARAM_CACHE_LIFETIME]) 
					? $params[Pandamp_Widget::PARAM_CACHE_LIFETIME] 
					: null;
					
		$cache 		 = Pandamp_Cache::getInstance();
		$widgetClass = ucfirst($module) . '_Widgets_' . ucfirst($name) . '_Widget';
		if (!class_exists($widgetClass)) {
			/**
			 * TODO: Should we inform to user that the widget does not exist
			 */
			return '';
		}
				
		if ($cache && $timeout != null) {
			/**
			 * The cache key ensure we will get the same cached value
			 * if the widget has been cached on other pages
			 */
			$cacheKey = $widgetClass . '_' . md5($module . '_' . $name . '_' . serialize($params));
			$cache->setLifetime($timeout);
			
			if (!($fromCache = $cache->load($cacheKey))) {
				$widget  = new $widgetClass($module, $name);
				$content = $widget->show($params);
				$cache->save($content, $cacheKey, array($module . '_Widgets'));
				return $content;
			} else {
				return $fromCache;
			}
		} else {
			$widget = new $widgetClass($module, $name);
			return $widget->show($params);
		}
	}
}
