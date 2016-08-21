<?php
class News_View_Helper_GetSource
{
	const EOL = "\n";
	
	public function getSource($attributes = array())
	{
		$selectedId = isset($attributes['selected']) ? $attributes['selected'] : null;
		
		$output = "<select name='$attributes[name]' id='$attributes[id]' class='form-control selectpicker' multiple>" . self::EOL;
	
		$criteria = [
			'type'=>'source',
			'status'=>'publish',
		];
		$sources = News_Models_Post::all($criteria)->sort(['title'=>1])->limit(5)->skip(0);
		foreach ($sources as $source) 
		{
			$sel = self::searchForId($source->getId(), $selectedId);
			$selected = ($sel != $source->getId()) ? '' : ' selected="selected"';
			$output .= sprintf('<option value="%s"%s>%s</option>',$source->getId(),$selected,$source->title) . self::EOL;
		}
		$output .= '</select>' . self::EOL;
	
		return $output;
	}
	function searchForId($id, $array) {
		if (is_array($array) && isset($array) && !empty($array)) {
			foreach ($array as $key => $val) {
				if ($val == $id) {
					return $val;
				}
			}
		}
		return null;
	}
}