<?php
class Tag_View_Helper_Gtagslug
{
	public function gtagslug($tag)
	{
		$tag = Tag_Models_Tag::one(['tag_text' => ucwords(strtolower($tag))]);
		if ($tag)
			return $tag->slug_tag_text;
		else
			return;
		
	}
}