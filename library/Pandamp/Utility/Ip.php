<?php
namespace Pandamp\Utility;
class Ip
{
	public static function getHttpRealIp() {
		if ( isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR']) ) {
			$ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
			$_SERVER['REMOTE_ADDR'] = trim($ips[0]);
		} elseif ( isset($_SERVER['HTTP_X_REAL_IP']) && !empty($_SERVER['HTTP_X_REAL_IP']) ) {
			$_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_X_REAL_IP'];
		} elseif ( isset($_SERVER['HTTP_CLIENT_IP']) && !empty($_SERVER['HTTP_CLIENT_IP']) ) {
			$_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_CLIENT_IP'];
		}
	
		return $_SERVER['REMOTE_ADDR'];
	}
	
}