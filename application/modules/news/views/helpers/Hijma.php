<?php
class News_View_Helper_Hijma extends Zend_View_Helper_Abstract
{
	public function hijma($selected)
	{
		$text = file_get_contents("http://www.jadwalsholat.org/adzan/monthly.php?id=".$selected);
		$list = $arr = [];
		if (isset($text) && !empty($text)) {
			libxml_use_internal_errors( true);
			$dom = new \DOMDocument;
			$dom->loadHTML( $text );
			$rows = array();
			foreach( $dom->getElementsByTagName( 'tr' ) as $tr ) {
				$cells = array();
				foreach( $tr->getElementsByTagName( 'td' ) as $td ) {
					$cells[] = $td->nodeValue;
				}
				$rows[] = $cells;
			}
			$e = self::searchForId(date('d'), $rows);
			$e = array_filter($e);
			reset($e);
			$key = key($e);
			$tdate = $e[$key];
			unset($e[$key]);
			$prayTime = array('Imsyak','Shubuh','Terbit','Dhuha','Dzuhur','Ashr','Maghrib','Isya');
			$list = array_combine($prayTime, array_values($e));
		}
		$this->view->assign('prayTime',$list);
		$this->view->assign('selectedCity',$selected);
		return $this->view->render('_partial/hijma.phtml');
	}
	function searchForId($id, $array) {
		foreach ($array as $key => $val) {
			if ($val[0] === $id) {
				return $val;
			}
		}
		return null;
	}
}