<?php
class Category_Models_Category extends Shanty_Mongo_Document
{
	protected static $_db = 'pandamp';
	protected static $_collection = 'category';

	public function fetchChildren($parentGuid)
	{
		$js = "function() {
    		return this.parent == this._id;
		}";
		$js1 = "function() {
    		return this.parent != this._id;
		}";
		if($parentGuid == 'root') {
			$wh = array('$where' => $js);
		}
		else
		{
			$wh = array('parent' => $parentGuid,'$where' => $js1);
		}
		return $this::all($wh)->sort(array('title' => 1));
	}
	
	public function findById($categorId)
	{
		if (null == $categorId || '' == $categorId) {
			return null;
		}
		
		if (strpos($categorId, 'mp') !== false) {
			$id = $categorId;
		}
		else
			$id = new MongoId($categorId);
		
		return $this::fetchOne(['_id' => $id]);
	}
}