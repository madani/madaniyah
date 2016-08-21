<?php
class Core_View_Helper_LocationSelect extends Zend_View_Helper_Abstract
{
	public function locationSelect($locationId=NULL)
	{
		$arrayLocation = [
			0 => "Choose Location",
			"Nanggroe Aceh Darussalam",
			"Sumatera Utara",
			"Sumatera Barat",
			"Riau",
			"Jambi",
			"Sumatera Selatan",
			"Bengkulu",
			"Lampung",
			"Kepulauan Bangka Belitung",
			"Kepulauan Riau",
			"DKI Jakarta",
			"Jawa Barat",
			"Jawa Tengah",
			"Daerah Istimewa Yogyakarta",
			"Jawa Timur",
			"Banten",
			"Bali",
			"Nusa Tenggara Barat",
			"Nusa Tenggara Timur",
			"Kalimantan Barat",
			"Kalimantan Tengah",
			"Kalimantan Selatan",
			"Kalimantan Timur",
			"Sulawasi Utara",
			"Sulawesi Tengah",
			"Sulawesi Selatan",
			"Sulawesi Tenggara",
			"Gorontalo",
			"Sulawesi Barat",
			"Maluku",
			"Maluku Utara",
			"Papua",
			"N/A",
			"Kalimantan Utara",
			"Papua Barat"
		];
		
		$location = "<select name='location' id='location' class='form-control'>\n";
		foreach ($arrayLocation as $key => $val)
		{
			$sel = (isset($locationId) && ($key == abs($locationId))) ? " selected" : "";
			$location .= "<option value=".$key . $sel.">".$val."</option>";
		}
		
		$location .= "</select>\n\n";
		return $location;
	}
}