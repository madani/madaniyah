<?php
namespace Pandamp\Controller\Action\Helper;
/**
 *
 * @author marcus brasizza
 *
 */
class googleApiClass {
	private $fromAddress;
	private $toAddress;
	private $time;
	private $distance;
	private $returnXml;
	private $points;
	private $countSteps;
	private $centerStep;
	private $imgBuffer;
	private $language;
	private $imageOutput;
	private $zoom;
	private $scale;
	private $urlXml;
	private $urlImage;
	private $instructions;
	function __construct() {
		$this->setUrlXml ( "http://maps.google.com/maps/api/directions/xml?language=%lang%&uid=" . uniqid () . "&sensor=true&origin=%origin%&destination=%destination%" );
		$this->setUrlImage ( "http://maps.googleapis.com/maps/api/staticmap?scale=%scale%&center=%centerScale%&size=800x600&sensor=true&maptype=hybrid&path=weight:3%7Ccolor:red%7Cenc:%points%&markers=color:blue%7Clabel:Origem%7C%origin%%&markers=color:blue%7Clabel:Destino%7C%destination%&format=%format%" );
	}
	final protected function __required() {
		$req = array (
				'fromAddress',
				'toAddress',
				'language',
				'imageOutput',
				'zoom',
				'scale'
		);
		$outputImage = array (
				'jpg',
				'png'
		);
		 
		$outputScale = array (
				'1',
				'2'
		);
		 
		foreach ( $req as $field ) {
			$bean = 'get' . ucfirst ( $field );
			switch ($field) {
				case 'imageOutput' :
					if (! in_array ( $this->$bean (), $outputImage )) {
						throw new Exception ( "Image supports only " . implode ( ',', $outputImage ) );
					}
					break;
					 
				case 'scale' :
					if (! in_array ( $this->$bean (), $outputScale )) {
						throw new Exception ( "Scale supports only " . implode ( ',', $outputScale ) );
					}
					break;
					 
				case 'zoom' :
					if (! is_int ( $this->$bean () )) {
						throw new Exception ( "Field {$field} is not a number" );
					}
					break;
					 
				default :
					if (! strlen ( $this->$bean () )) {
						throw new Exception ( "Field {$field} is empty" );
					}
					break;
			}
		}
		return true;
	}
	/**
	 *
	 * @return the $instructions
	 */
	public function getInstructions() {
		return $this->instructions;
	}
	 
	/**
	 *
	 * @param field_type $instructions
	 */
	public function setInstructions($instructions) {
		$this->instructions = $instructions;
	}
	public function findAddress() {
		if ($this->__required ()) {
			 
			$xml = $this->getUrlXml ();
			$xml = str_replace ( array (
					'%lang%',
					'%origin%',
					'%destination%'
			), array (
					$this->getLanguage (),
					$this->getFromAddress (),
					$this->getToAddress ()
			), $xml );
			 
			$this->setUrlXml ( $xml );
			 
			if (function_exists ( 'curl_init' )) {
				$ch = curl_init ();
				curl_setopt ( $ch, CURLOPT_URL, $this->getUrlXml () );
				curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
				curl_setopt ( $ch, CURLOPT_REFERER, $_SERVER ['HTTP_HOST'] );
				$returnXml = curl_exec ( $ch );
				$this->setReturnXml ( $returnXml );
				curl_close ( $ch );
			} else {
				$returnXml = file_get_contents ( $this->getReturnXml () );
				$this->setReturnXml ( $returnXml );
			}
			 
			if (! $returnXml) {
				throw new Exception ( "Cannot receive the xml data" );
			} else {
				$simple = simplexml_load_string ( $returnXml );
				 
				if (( string ) $simple->status != 'OK') {
					throw new Exception ( ( string ) $simple->status );
				} else {
					$totalSteps = count ( $simple->route->leg->step );
					$this->setPoints ( ( string ) $simple->route->overview_polyline->points );
					$this->setCountSteps ( count ( $simple->route->leg->step ) );
					$this->setTime ( ( string ) $simple->route->leg->duration->value );
					$this->setDistance ( ( string ) $simple->route->leg->distance->value );
					for($i = 0; $i != ($totalSteps); $i ++) {
						$route [] = $simple->route->leg->step [$i];
					}
					$this->setInstructions ( $route );
				}
			}
		}
		 
		return $this;
	}
	final function withImage($centerStep = null, $path = null) {
		if ($this->getReturnXml ()) {
			$urlImage = $this->getUrlImage ();
			 
			if (! $centerStep) {
				throw new Exception ( "Need a center value between 0 and " . $this->getCountSteps () );
			} else {
				if (($centerStep > $this->getCountSteps ()) or $centerStep < 0) {
					throw new Exception ( "{$centerStep} is not between 0 and " . $this->getCountSteps () );
				} else {
					 
					$instructions = $this->getInstructions ();
					 
					$lat = ($instructions [$centerStep]->start_location->lat);
					$lng = ($instructions [$centerStep]->start_location->lng);
					$this->setCenterStep ( "{$lat},{$lng}" );
				}
			}
			 
			$urlImage = str_replace ( array (
					'%scale%',
					'%centerScale',
					'%points%',
					'%origin',
					'%destination',
					'%format'
			), array (
					$this->getScale (),
					$this->getCenterStep (),
					$this->getPoints (),
					$this->getFromAddress (),
					$this->getToAddress (),
					$this->getImageOutput ()
			), $urlImage );
			 
			$this->setUrlImage ( $urlImage );
			if (function_exists ( 'curl_init' )) {
				$ch = curl_init ();
				curl_setopt ( $ch, CURLOPT_URL, $this->getUrlImage () );
				curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
				curl_setopt ( $ch, CURLOPT_REFERER, $_SERVER ['HTTP_HOST'] );
				$imgBuffer = curl_exec ( $ch );
				$this->setImgBuffer ( $imgBuffer );
				curl_close ( $ch );
			} else {
				$imgBuffer = file_get_contents ( $this->getUrlImage () );
				$this->setImgBuffer ( $imgBuffer );
			}
			 
			if (! $imgBuffer) {
				throw new Exception ( "Cannot receive the xml data" );
			} else {
				 
				if ($path) {
					file_put_contents ( $path, $imgBuffer );
				}
				 
				return $this;
			}
		} else {
			throw new Exception ( "No XML DATA" );
		}
	}
	function __destruct() {
	}
	/**
	 *
	 * @return the $urlImage
	 */
	public function getUrlImage() {
		return $this->urlImage;
	}
	 
	/**
	 *
	 * @param field_type $urlImage
	 */
	public function setUrlImage($urlImage) {
		$this->urlImage = $urlImage;
	}
	 
	/**
	 *
	 * @return the $urlXml
	 */
	public function getUrlXml() {
		return $this->urlXml;
	}
	 
	/**
	 *
	 * @param string $urlXml
	 */
	public function setUrlXml($urlXml) {
		$this->urlXml = $urlXml;
	}
	 
	/**
	 *
	 * @return the $scale
	 */
	public function getScale() {
		return $this->scale;
	}
	 
	/**
	 *
	 * @param field_type $scale
	 */
	public function setScale($scale) {
		$this->scale = $scale;
	}
	 
	/**
	 *
	 * @return the $fromAddress
	 */
	public function getFromAddress() {
		return $this->fromAddress;
	}
	 
	/**
	 *
	 * @return the $toAddress
	 */
	public function getToAddress() {
		return $this->toAddress;
	}
	 
	/**
	 *
	 * @return the $time
	 */
	public function getTime() {
		return $this->time;
	}
	 
	/**
	 *
	 * @return the $distance
	 */
	public function getDistance() {
		return $this->distance;
	}
	 
	/**
	 *
	 * @return the $returnXml
	 */
	public function getReturnXml() {
		return $this->returnXml;
	}
	 
	/**
	 *
	 * @return the $points
	 */
	public function getPoints() {
		return $this->points;
	}
	 
	/**
	 *
	 * @return the $countSteps
	 */
	public function getCountSteps() {
		return $this->countSteps;
	}
	 
	/**
	 *
	 * @return the $centerStep
	 */
	public function getCenterStep() {
		return $this->centerStep;
	}
	 
	/**
	 *
	 * @return the $imgBuffer
	 */
	public function getImgBuffer() {
		return $this->imgBuffer;
	}
	 
	/**
	 *
	 * @return the $language
	 */
	public function getLanguage() {
		return $this->language;
	}
	 
	/**
	 *
	 * @return the $imageOutput
	 */
	public function getImageOutput() {
		return $this->imageOutput;
	}
	 
	/**
	 *
	 * @return the $zoom
	 */
	public function getZoom() {
		return $this->zoom;
	}
	 
	/**
	 *
	 * @param field_type $fromAddress
	 */
	public function setFromAddress($fromAddress) {
		$this->fromAddress = str_replace ( ' ', '+', $this->strNoAcentos ( $fromAddress ) );
	}
	 
	/**
	 *
	 * @param field_type $toAddress
	 */
	public function setToAddress($toAddress) {
		$this->toAddress = str_replace ( ' ', '+', $this->strNoAcentos ( $toAddress ) );
	}
	 
	/**
	 *
	 * @param field_type $time
	 */
	public function setTime($time) {
		$this->time = $time;
	}
	 
	/**
	 *
	 * @param field_type $distance
	 */
	public function setDistance($distance) {
		$this->distance = $distance;
	}
	 
	/**
	 *
	 * @param field_type $returnXml
	 */
	public function setReturnXml($returnXml) {
		$this->returnXml = $returnXml;
	}
	 
	/**
	 *
	 * @param field_type $points
	 */
	public function setPoints($points) {
		$this->points = $points;
	}
	 
	/**
	 *
	 * @param field_type $countSteps
	 */
	public function setCountSteps($countSteps) {
		$this->countSteps = $countSteps;
	}
	 
	/**
	 *
	 * @param field_type $centerStep
	 */
	public function setCenterStep($centerStep) {
		$this->centerStep = $centerStep;
	}
	 
	/**
	 *
	 * @param field_type $imgBuffer
	 */
	public function setImgBuffer($imgBuffer) {
		$this->imgBuffer = $imgBuffer;
	}
	 
	/**
	 *
	 * @param field_type $language
	 */
	public function setLanguage($language) {
		$this->language = $language;
	}
	 
	/**
	 *
	 * @param field_type $imageOutput
	 */
	public function setImageOutput($imageOutput) {
		$this->imageOutput = $imageOutput;
	}
	 
	/**
	 *
	 * @param field_type $zoom
	 */
	public function setZoom($zoom) {
		$this->zoom = $zoom;
	}
	public function strNoAcentos($msg) {
		 
		/**
		 * Bloco do A *
		 */
		$msg = str_replace ( 'Ã¡', 'a', $msg );
		$msg = str_replace ( 'Ã', 'A', $msg );
		$msg = str_replace ( 'Ã ', 'a', $msg );
		$msg = str_replace ( 'Ã€', 'A', $msg );
		$msg = str_replace ( 'Ã¢', 'a', $msg );
		$msg = str_replace ( 'Ã‚', 'A', $msg );
		$msg = str_replace ( 'Ã£', 'a', $msg );
		$msg = str_replace ( 'Ãƒ', 'A', $msg );
		$msg = str_replace ( 'Ã¤', 'a', $msg );
		$msg = str_replace ( 'Ã„', 'A', $msg );
		 
		/**
		 * Bloco do E *
		*/
		$msg = str_replace ( 'Ã©', 'e', $msg );
		$msg = str_replace ( 'Ã‰', 'E', $msg );
		$msg = str_replace ( 'Ã¨', 'e', $msg );
		$msg = str_replace ( 'Ãˆ', 'E', $msg );
		$msg = str_replace ( 'Ãª', 'e', $msg );
		$msg = str_replace ( 'ÃŠ', 'E', $msg );
		$msg = str_replace ( 'Ã«', 'e', $msg );
		$msg = str_replace ( 'Ã‹', 'E', $msg );
		 
		/**
		 * Bloco do I *
		*/
		$msg = str_replace ( 'i', 'i', $msg );
		$msg = str_replace ( 'Ã­', 'i', $msg );
		$msg = str_replace ( 'Ã', 'I', $msg );
		$msg = str_replace ( 'Ã¬', 'i', $msg );
		$msg = str_replace ( 'ÃŒ', 'I', $msg );
		$msg = str_replace ( 'Ã®', 'i', $msg );
		$msg = str_replace ( 'ÃŽ', 'I', $msg );
		$msg = str_replace ( 'Ä©', 'i', $msg );
		$msg = str_replace ( 'Ä¨', 'I', $msg );
		$msg = str_replace ( 'Ã¯', 'a', $msg );
		$msg = str_replace ( 'Ã', 'I', $msg );
		 
		/**
		 * Bloco do O *
		*/
		$msg = str_replace ( 'Ã³', 'o', $msg );
		$msg = str_replace ( 'Ã“', 'O', $msg );
		$msg = str_replace ( 'Ã²', 'o', $msg );
		$msg = str_replace ( 'Ã’', 'O', $msg );
		$msg = str_replace ( 'Ã´', 'o', $msg );
		$msg = str_replace ( 'Ã”', 'O', $msg );
		$msg = str_replace ( 'Ãµ', 'o', $msg );
		$msg = str_replace ( 'Ã•', 'O', $msg );
		$msg = str_replace ( 'Ã¶', 'o', $msg );
		$msg = str_replace ( 'Ã–', 'O', $msg );
		 
		/**
		 * Bloco do U *
		*/
		$msg = str_replace ( 'Ãº', 'u', $msg );
		$msg = str_replace ( 'Ãš', 'U', $msg );
		$msg = str_replace ( 'Ã¹', 'u', $msg );
		$msg = str_replace ( 'Ã™', 'U', $msg );
		$msg = str_replace ( 'Ã»', 'u', $msg );
		$msg = str_replace ( 'Ã›', 'U', $msg );
		$msg = str_replace ( 'Å©', 'u', $msg );
		$msg = str_replace ( 'Å¨', 'U', $msg );
		$msg = str_replace ( 'Ã¼', 'u', $msg );
		$msg = str_replace ( 'Ãœ', 'U', $msg );
		 
		/**
		 * Bloco do Ã‡ *
		*/
		$msg = str_replace ( 'Ã§', 'c', $msg );
		$msg = str_replace ( 'Ã‡', 'C', $msg );
		 
		return $msg;
	}
}
