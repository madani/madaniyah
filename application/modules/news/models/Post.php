<?php
class News_Models_Post extends Shanty_Mongo_Document
{
	protected static $_db = 'pandamp';
	protected static $_collection = 'posts';
	
	public static function increaseViews($slug)
	{
		return self::getMongoDb()->command([
			'findAndModify' => 'posts',
			'query' => [
				'slug' => strtolower($slug)
			],
			'update' => [
				'$inc' => ['num_views' => 1]
			],
			'new' => true
		]);
	}
}