<?php
class Tag_Models_Tag extends Shanty_Mongo_Document
{
	protected static $_db = 'pandamp';
	protected static $_collection = 'tag';
	
	public function increaseViews($slug)
	{
		return self::getMongoDb()->command([
				'findAndModify' => 'tag',
				'query' => [
					'slug_tag_text' => strtolower($slug)
				],
				'update' => [
					'$inc' => ['frequency' => 1]
				],
				'new' => true
				]);
	}
}