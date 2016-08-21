<?php
class News_Widgets_Relatedarticle_Widget extends Pandamp_Widget
{
	protected function _prepareShow() 
	{
		// dapatkan data related artikel selain artikel ini
		$criteria = [
			'_id' => ['$ne' => $this->_view->layout()->articleId],
			'status' => 'publish' 
		];
		
		if (! empty($this->_view->layout()->tags)) 
			$criteria['fields.tags'] = ['$in' => $this->_view->layout()->tags];
		else
			$criteria['title'] = new \MongoRegex("/".$this->_view->layout()->title."/i");
		
		$data = News_Models_Post::all($criteria)->limit(3)->skip(0);
		
		// jika tidak ada berdasarkan tag, cari berdasarkan kategori
		if (count($data->export()) < 1 && isset($this->_view->layout()->category)) {
			unset(
				$criteria['title'],
				$criteria['fields.tags']	
			);
			
			$criteria['fields.category'] = [
				'$elemMatch' => [
					'categoryId' => $this->_view->layout()->category
				]
			];
			
			$data = News_Models_Post::all($criteria)->sort(['date.update' => -1])->limit(3)->skip(0);
			
			
		}
		
		$this->_view->assign('data',$data);
	}
}
