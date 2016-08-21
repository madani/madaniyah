<?php
class News_View_Helper_GetAuthor
{
	const EOL = "\n";

	public function getAuthor($attributes = array())
	{
		$selectedId = isset($attributes['selected']) ? $attributes['selected'] : null;

		$output = "<select name='$attributes[name]' id='$attributes[id]' class='form-control'>" . self::EOL
		. '<option value="">---</option>' . self::EOL;

		$criteria = [
			'type'=>'author',
			'status'=>'publish',
		];
		$authors = News_Models_Post::all($criteria)->sort(['title'=>1]);
		foreach ($authors as $author)
		{
			$selected = ($selectedId != $author->getId()) ? '' : ' selected="selected"';
			$output .= sprintf('<option value="%s"%s>%s</option>',$author->getId(),$selected,$author->title) . self::EOL;
		}
		$output .= '</select>' . self::EOL;

		return $output;
	}

}