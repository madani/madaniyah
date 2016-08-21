<?php
class Dev_CatalogController extends Zend_Controller_Action
{
	public function fixcatAction()
	{
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		
		$posts = News_Models_Post::all();
		
		foreach ($posts as $post) {
			if (isset($post->fields['category']['categoryId'])) {
				$c = [];
				$c[] = [
					'categoryId' => $post->fields['category']['categoryId'],
					'name' => $post->fields['category']['name'],
					'slug' => $post->fields['category']['slug']
				];
				
				$post->fields['category'] = $c;
				
				News_Models_Post::update(['_id' => new MongoId($post->getId())],$post->export());
			}
			
			
		}
		
		
	}
}