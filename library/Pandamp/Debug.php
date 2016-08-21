<?php

class Pandamp_Debug
{
	public static function manager($data, $stop=false)
	{
		echo '<pre>';
		print_r($data);
		echo '</pre>';
		if ($stop) die;
	}
}