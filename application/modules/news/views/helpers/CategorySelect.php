<?php
class News_View_Helper_CategorySelect
{
	const EOL = "\n";
	
	/**
	 * Display select box listing all categories
	 *
	 * @param $attributes array
	 * @return string
	 */
	public function categorySelect($attributes = array())
	{
		$selectedId = isset($attributes['selected']) ? $attributes['selected'] : null;
		
		$output = "<select name='$attributes[name]' id='$attributes[id]' class='form-control select2' multiple data-placeholder='Select Category'>" . self::EOL
		. '<option value="">---</option>' . self::EOL;
		
		$output .= $this->_traverseFolder('root','', 0, $selectedId);
		
		$output .= '</select>' . self::EOL;
		
		return $output;
	}
	
	/**
	 * Get Tree
	 *
	 * @param string $folderGuid
	 * @param string $sGuid
	 * @param int $level
	 * @return void
	 */
	protected function _traverseFolder($folderGuid, $sGuid, $level, $selectedId=null)
	{
		$categoryService = new Category_Models_Category();
		
		$rowSet = $categoryService->fetchChildren($folderGuid);
		$sGuid = '';
	
		foreach($rowSet as $row)
		{
			$selected = ($selectedId != $row['_id']) ? '' : ' selected="selected"';

			/**
			 * @todo
			 * @var mp54c22b649be7e == Jual Beli
			 */
			if (in_array($row['_id'], array('mp54c22b649be7e'))) 
				$option = '<option value="'.$row['_id'].'"'.$selected.' disabled>'.str_repeat('-----', $level).$row['name'].'</option>';
			else
				$option = '<option value="'.$row['_id'].'"'.$selected.'>'.str_repeat('-----', $level).$row['name'].'</option>';
				
			$sGuid .= $option . $this->_traverseFolder($row['_id'], '', $level+1, $selectedId);
		}
		return $sGuid;
	}
}