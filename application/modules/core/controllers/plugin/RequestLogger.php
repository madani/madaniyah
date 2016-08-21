<?php
/**
 * @author	2011-2014 Nihki Prihadi
 * @version $Id: RequestLogger.php 1 2012-02-27 19:38Z $
 */
use Pandamp\Utility\Ip;
class Core_Controllers_Plugin_RequestLogger extends Zend_Controller_Plugin_Abstract
{
	/** 
	 * Some popular bots
	 * <Bot agent pattern> => <Bot name>
	 * @var array
	 */
	private static $_BOTS = array(
		'/googlebot/i' 	   => 'google',
		'/msnbot/i' 	   => 'bing',
		'/bingbot/i' 	   => 'bing',
		'/slurp/i' 		   => 'yahoo',
		'/baidu/i' 		   => 'baidu',
		'/twiceler/i' 	   => 'cuil',
		'/teoma/i' 		   => 'ask',
		'/facebook/i' 	   => 'facebook',
		'/technoratibot/i' => 'technorati',
		'/yandexbot/i'	   => 'yandex',		
	);
	
	/**
	 * Most popular web browsers
	 * 
	 * @var array
	 */
	private static $_BROWSERS = array(
		'firefox', 'msie', 'opera', 
		'chrome', 'safari', 
		'mozilla', 'seamonkey', 'konqueror', 'netscape', 
		'gecko', 'navigator', 'mosaic', 'lynx', 'amaya', 
		'omniweb', 'avant', 'camino', 'flock', 'aol',
	);
	
	public function postDispatch(Zend_Controller_Request_Abstract $request) 
	{
		if ($request->isXmlHttpRequest()) {
			return;
		}
		
		$uri 	 = $request->getRequestUri();
		$agent 	 = $request->getServer('HTTP_USER_AGENT');
		$browser = self::_getBrowserInfo($agent);
		
		$ip = (new Ip)->getHttpRealIp();
				
		$log = [
			'ip' 		  => $ip,
			'agent' 	  => $agent,
			'browser' 	  => $browser['browser'],
			'version' 	  => $browser['version'],
			'platform'	  => $browser['platform'],
			'bot' 		  => self::_getBot($agent),
			'uri' 		  => $uri,
			'full_url' 	  => $request->getScheme().'://'.$request->getHttpHost().'/'.ltrim($uri, '/'),
			'refer_url'   => $request->getServer('HTTP_REFERER'),
			'access_time' => new \MongoDate(),
		];
		
		$auth = Zend_Auth::getInstance();
		if ($auth->hasIdentity()) {
			$log['kopel'] = $auth->getIdentity()->getId();	
		}
		
		/**
		 * Finding out the location details using the ip address
		 */
		$query = @unserialize(file_get_contents('http://ip-api.com/php/'.$ip));
		if($query && $query['status'] == 'success') {
			$log['country'] = $query['country'];
			$log['city'] = $query['city'];
			$log['latitude'] = $query['lat'];
			$log['longitude'] = $query['lon'];
		}
		
		Core_Models_RequestLog::insert($log);
	}

	private static function _getBrowserInfo($agent) 
	{
		$agent = strtolower($agent);
		$info  = array('browser' => null, 'version' => null, 'platform' => null);
		foreach(self::$_BROWSERS as $browser) { 
            if (preg_match('#(' . $browser . ')[/ ]?([0-9.]*)#', $agent, $match)) { 
                $info['browser'] = $match[1] ; 
                $info['version'] = $match[2] ; 
                break;
            }
        }
		if (preg_match('/linux/', $agent)) { 
            $info['platform'] = 'linux'; 
        } elseif (preg_match('/macintosh|mac os x/', $agent)) { 
            $info['platform'] = 'mac'; 
        } elseif (preg_match('/windows|win32/', $agent)) { 
            $info['platform'] = 'windows';
        }
		return $info; 
	}
	
	private static function _getBot($agent) 
	{
		foreach (self::$_BOTS as $pattern => $name) {
			if (preg_match($pattern, $agent) == 1) {
				return $name;
			}
		}
		return null;
	}
}