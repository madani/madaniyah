<?php
class News_View_Helper_CatalogStatus
{
	public function catalogStatus($status=NULL)
	{
		$arrayStatus = [
			"unpublish" => "UnPublish",
			"publish" => "Publish"
		];
		
		$location = "<select name='status' class='form-control'>\n";
		foreach ($arrayStatus as $key => $val)
		{
			$sel = (isset($status) && ($key == $status)) ? " selected" : "";
			$location .= "<option value=".$key . $sel.">".$val."</option>";
		}
		
		$location .= "</select>\n\n";
		return $location;
	}
}