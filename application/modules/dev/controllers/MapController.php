<?php
require_once("vendor/autoload.php");
use Redbox\Distance,
	Pandamp\Controller\Action\Helper\googleApiClass;
class Dev_MapController extends Zend_Controller_Action
{
	public function redboxAction()
	{
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		
		$p1 = new Distance\GeoPoint(52.364533, 4.820374); /* Amsterdam */
		$p2 = new Distance\GeoPoint(51.925538, 4.471867); /* Rotterdam */
		
		
		$tool = new Distance\CalculateDistance();
		$distance = $tool->setSource($p1)
		->setDestination($p2)
		->setUseSslVerifier(false)
		->getDistanceInKM();
		
		echo "<h2>Example 1</h2><br>";
		echo 'The calculated distance is: '.$distance.' KM<br>';
	}
	
	public function sample1Action()
	{
		$google = new googleApiClass();
		
		$google->setFromAddress('England');
		$google->setToAddress('France');
		$google->setLanguage('us');
		$google->setImageOutput('png');
		$google->setZoom(14);
		$google->setScale('1');
		try{
			$pathImg = dirname($_SERVER['SCRIPT_FILENAME']) . DS .'fs/mapMe.png';
			$google->findAddress()->withImage(5,$pathImg);
			 
			echo "From: ". $google->getFromAddress().'<br>';
			echo "To: ". $google->getToAddress().'<br>';
			echo "Distance: ".($google->getDistance()).' meters<br>';
			echo "Time: ".($google->getTime()).' seconds<br>';
			echo " {$google->getCountSteps()} steps between from and to address<br>";
			echo "<hr><strong>Steps</strong>";
					echo "<pre>";
							print_r($google->getInstructions());
							echo "</pre>";
									if(!$google->getImgBuffer()){
									echo "No image was requested";
									}else{
										echo "Followed Image <br>";
										echo "Scale: ".$google->getScale().'<br>';
										echo "Center: ".$google->getCenterStep().'<br>';
										echo "Zoom: ".$google->getZoom().'<br>';
										
										echo "<img src='{$pathImg}'><br>";
										 
									}
									 
									 
		}catch(Exception $e ){
			echo $e->getMessage();
		}
	}
}