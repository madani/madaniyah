<?php
use Pandamp\Utility\Formatting;
class News_ArticleController extends Zend_Controller_Action
{
	public function addAction()
	{
		$request = $this->getRequest();
		
		if ($request->isPost()) {
			$filename = $request->getPost('filename');
			$image = $request->getPost('img');
			$tags = $request->getPost('tags');
			$category = $request->getPost('category');
			$title = $request->getPost('title');
			$content = $request->getPost('content');
			$condition = $request->getPost('condition');
			$type = $request->getPost('type');
			$price = $request->getPost('price');
			$location = $request->getPost('location');
			$terms = $request->getPost('terms');
			$createdDate = $request->getPost('createdDate');
			$pubDate = $request->getPost('publishedDate');
			$expDate = $request->getPost('expiredDate');
			$status = $request->getPost('status');
			$question = $request->getPost('question');
			$typesource = $request->getPost('typesource');
			$origin = $request->getPost('origin');
			$sticky = $request->getPost('sticky');
			$allowComment = $request->getPost('allowComment');
			
			$auth = Zend_Auth::getInstance()->getIdentity();
			
			$now = new \MongoDate();
			
			$data = new News_Models_Post([
				'author' => [
					'id' => (string) $auth['_id']->{'$id'},
					'name' => implode(' ', [$auth->first_name, $auth->last_name]),
					'username' => $auth->user_name,
				],
				'editor' => [
					'id' => (string) $auth['_id']->{'$id'},
					'name' => implode(' ', [$auth->first_name, $auth->last_name]),
				],
				'slug' => (new Pandamp_Utility_Posts)->sanitize_post_name($title),
				'title' => (new Pandamp_Utility_Posts)->sanitize_post_title($title),
				'content' => (new Pandamp_Utility_Posts)->sanitize_post_content($content),
				'status' => $status,
				'date' => [
					'create' => new \MongoDate(strtotime($createdDate)),
					'update' => $now,
					'publish' => (isset($pubDate) && ! empty($pubDate)) ? new \MongoDate(strtotime($pubDate)) : '0000-00-00 00:00:00',
					'expire' => (isset($expDate) && ! empty($expDate)) ? new \MongoDate(strtotime($expDate)) : '0000-00-00 00:00:00'
				],
			]);
			
			$arrs = [];
			
			if (isset($question) && !empty($question)) {
				$data->question = $question;
			}
			
			if (isset($sticky) && !empty($sticky)) {
				$data->sticky = $sticky;
			}
			else 
				unset($data->sticky);
			
			if (isset($category) && ! empty($category)) {
				/*$cat = (new Category_Models_Category)->findById($category);
				if ($cat) {
					$c = [
						'category' => [
							'categoryId' => (string) $cat->getId(),
							'name' => $cat->name,
							'slug' => $cat->slug
						],
					];
					array_push($arrs, $c);
						
				}*/

				foreach ($category as $i => $c)
					$category[$i] = $c;
					
				
				$cats = Category_Models_Category::all([
						'_id' => ['$in' => $category]
						]);
				
				$c = [];
				foreach ($cats as $cat) {
					$c['category'][] = [
					'categoryId' => (string) $cat->getId(),
					'name' => $cat->name,
					'slug' => $cat->slug
					];
				}
				
				array_push($arrs, $c);
			}
			
			if (isset($tags) && ! empty($tags)) {
				$t = [
					'tags' => array_map('trim', explode(',', $tags)),
				];
				array_push($arrs, $t);
				foreach ($t['tags'] as $tag) {
					$tagTitle = ucwords(strtolower($tag));
					$tagSlug = (new Pandamp_Utility_Posts)->sanitize_post_name($tagTitle);
				
					$tagDb = Tag_Models_Tag::fetchOne(['slug_tag_text' => $tagSlug]);
					if (!$tagDb) {
						Tag_Models_Tag::insert([
							'tag_text' => $tagTitle,
							'slug_tag_text' => $tagSlug,
							'created_date' => new \MongoDate()
						]);
					}
				}
				
			}
			
			$m['media'] = 'text';
				
			if ( preg_match('/youtube/', $content) )
				$m['media'] = 'video';
				
			if ( preg_match('/soundcloud/', $content) )
				$m['media'] = 'audio';
			
			array_push($arrs, $m);
			if (is_array($filename)) {
				for ($i = 0; $i < count($filename); $i++) {
					$imageName = $filename[$i];
					$imageUrls = json_decode(stripslashes($image[$i]));
					foreach ($imageUrls as $key => $value) {
						$fileId = pathinfo($value->url,PATHINFO_FILENAME);
						$fileId = explode('_', $fileId);
						$img['images'][$i]['fileid'] = $fileId[0];
						$img['images'][$i]['createdDate'] = new \MongoDate();
						$img['images'][$i]['createdBy'] = $auth->user_name;
					
						$attr = $request->getPost('attr_'.$fileId[0]);
						$attrval = $request->getPost('attrval_'.$fileId[0]);
						if (isset($attr) && !empty($attr) && isset($attrval) && !empty($attrval)) {
							for ($x=0; $x<count(array_keys($attr)); $x++) {
								if (strtolower($attr[$x]) == 'title') {
									$img['images'][$i]['title'] = (new Pandamp_Utility_Posts)->sanitize_post_title($attrval[$x]);
									$img['images'][$i]['slug'] = (new Pandamp_Utility_Posts)->sanitize_post_name($attrval[$x]);
								}
								else
								{
									$img['images'][$i][strtolower($attr[$x])] = $attrval[$x];
								}
							}
						}
					
						$img['images'][$i][$key]['url'] = $value->url;
						$img['images'][$i][$key]['size'] = $value->size;
					}
						
				}
				
				array_push($arrs, $img);
			}
					
			$arrayFields = [];
			foreach ($arrs as $arr)
			{
				if (is_array($arr)) {
					$arrayFields = array_merge($arrayFields,$arr);
				}
			}
			
			$data->fields = $arrayFields;
				
			if (isset($condition)) {
				$data->condition = $condition;
			}
			else
				unset($data->condition);
				
			if (isset($type)) {
				$data->type = $type;
			}
				
			if ($price) {
				$data->price = $price;
			}
			else
				unset($data->price);
				
			if ($typesource) {
				$data->typesource = $typesource;
			}
			else
				unset($data->typesource);
				
			if ($origin) {
				$data->origin = $origin;
			}
			else
				unset($data->origin);
				
				
			if ($location != 0) {
				$data->location = $location;
			}
			else
				unset($data->location);
				
				
			if (isset($allowComment) && $allowComment == 'on') {
				$data->allowComment = 'Y';
			}
				
			if (isset($terms) && $terms == 'on') {
				$data->terms = 'Y';
			}
				
			if (!$content)
				unset($data->content);
				
			unset($data->_type);
			//Pandamp_Debug::manager($data->export(),TRUE);	
			News_Models_Post::insert($data->export());
		}
	}
	
	public function editAction()
	{
		$request = $this->getRequest();
		
		$id = $request->getParam('id');
		
		$article = News_Models_Post::find($id);
		
		$config = Pandamp_Module_Config::getConfig('upload');
		$size = $config->size->toArray();
		$thumbs = array_keys($size);
		
		$out = [];
		foreach ($article->fields['category'] as $eCat) {
			array_push($out, $eCat['categoryId']);
		}
		$ocat = "'" . implode("','", $out) . "'";
		
		$this->_helper->layout()->selectedCategory = $ocat;
		
		$this->view->assign('article',$article);
		$this->view->assign('thumb',$thumbs);
		
		if ($request->isPost()) {
			$articleId = $request->getPost('postId');
			$title = $request->getPost('title');
			$content = $request->getPost('content');
			$question = $request->getPost('question');
			$category = $request->getPost('category');
			$tags = $request->getPost('tags');
			$filename = $request->getPost('filename');
			//$fileId = $request->getPost('fileid');
			$image = $request->getPost('img');
			$condition = $request->getPost('condition');
			$type = $request->getPost('type');
			$price = $request->getPost('price');
			$location = $request->getPost('location');
			$terms = $request->getPost('terms');
			$createdDate = $request->getPost('createdDate');
			$pubDate = $request->getPost('publishedDate');
			$expDate = $request->getPost('expiredDate');
			$status = $request->getPost('status');
			$source = $request->getPost('source');
			$typesource = $request->getPost('typesource');
			$origin = $request->getPost('origin');
			$answeredby = $request->getPost('answeredby');
			$sticky = $request->getPost('sticky');
			$allowComment = $request->getPost('allowComment');
				
			$auth = Zend_Auth::getInstance()->getIdentity();
				
			$now = new \MongoDate();
				
			$postArticle = News_Models_Post::find($articleId);
			
			$postArticle->author = [
				'id' => $postArticle->author['id'],
				'name' => $postArticle->author['name'],
				'username' => $postArticle->author['username']
			];
			$postArticle->editor = [
				'id' => (string) $auth->getId(),
				'name' => implode(' ', [$auth->first_name, $auth->last_name]),
			];
			$postArticle->date = [
				'create' => new \MongoDate(strtotime($createdDate)),
				'update' => $now,
				'publish' => (isset($pubDate) && ! empty($pubDate)) ? new \MongoDate(strtotime($pubDate)) : '0000-00-00 00:00:00',
				'expire' => (isset($expDate) && ! empty($expDate)) ? new \MongoDate(strtotime($expDate)) : '0000-00-00 00:00:00'
			];
			$postArticle->title = (new Pandamp_Utility_Posts)->sanitize_post_title($title);
			$postArticle->slug = (new Pandamp_Utility_Posts)->sanitize_post_name($title);
			$postArticle->content = (new Pandamp_Utility_Posts)->sanitize_post_content($content);
			$postArticle->status = $status;
				
			$arrs = [];
			
			if (isset($question) && !empty($question)) {
				$postArticle->question = $question;
			}
			else
				unset($postArticle->question);
				
			if (isset($source) && !empty($source)) {
				$postArticle->source = $source;
			}
			else
				unset($postArticle->source);
				
			if (isset($typesource) && !empty($typesource)) {
				$postArticle->typesource = $typesource;
			}
			else
				unset($postArticle->typesource);
				
			if (isset($origin) && !empty($origin)) {
				$postArticle->origin = $origin;
			}
			else
				unset($postArticle->origin);
				
			if (isset($answeredby) && !empty($answeredby)) {
				$postArticle->answeredby = $answeredby;
			}
			else
				unset($postArticle->answeredby);
			
			if (isset($category) && ! empty($category)) {
				foreach ($category as $i => $c)
					$category[$i] = $c;
					
				
				$cats = Category_Models_Category::all([
							'_id' => ['$in' => $category]
						]);
				
				$c = [];
				foreach ($cats as $cat) {
					$c['category'][] = [
							'categoryId' => (string) $cat->getId(),
							'name' => $cat->name,
							'slug' => $cat->slug
					];
				}
				
				/*$cat = (new Category_Models_Category)->findById($category);
				if ($cat) {
					$c = [
						'category' => [
							'categoryId' => (string) $cat->getId(),
							'name' => $cat->name,
							'slug' => $cat->slug
						],
					];
					array_push($arrs, $c);
				}*/
				
				array_push($arrs, $c);
			}	
			
			if (isset($tags) && ! empty($tags)) {
				$t = [
					'tags' => array_map('trim', explode(',', $tags)),
				];
				array_push($arrs, $t);
				foreach ($t['tags'] as $tag) {
					$tagTitle = ucwords(strtolower($tag));
					$tagSlug = (new Pandamp_Utility_Posts)->sanitize_post_name($tagTitle);
					
					$tagDb = Tag_Models_Tag::fetchOne(['slug_tag_text' => $tagSlug]);
					if (!$tagDb) {
						Tag_Models_Tag::insert([
							'tag_text' => $tagTitle,
							'slug_tag_text' => $tagSlug,
							'created_date' => new \MongoDate()
						]);
					}
						
				}
			}
			
			$m['media'] = 'text';
			
			if ( preg_match('/youtube/', $content) )
				$m['media'] = 'video';
			
			if ( preg_match('/soundcloud/', $content) )
				$m['media'] = 'audio';
				
			array_push($arrs, $m);
			if (is_array($filename)) {
				for ($i = 0; $i < count($filename); $i++) {
					$imageName = $filename[$i];
					$imageUrls = json_decode(stripslashes($image[$i]));
					foreach ($imageUrls as $key => $value) {
						$fileId = pathinfo($value->url,PATHINFO_FILENAME);
						$fileId = explode('_', $fileId);
						$img['images'][$i]['fileid'] = $fileId[0];
						$img['images'][$i]['createdDate'] = new \MongoDate();
						$img['images'][$i]['createdBy'] = $auth->user_name;
					
						$attr = $request->getPost('attr_'.$fileId[0]);
						$attrval = $request->getPost('attrval_'.$fileId[0]);
						if (isset($attr) && !empty($attr) && isset($attrval) && !empty($attrval)) {
							for ($x=0; $x<count(array_keys($attr)); $x++) {
								if ($attr[$x] == 'title') {
									$img['images'][$i]['title'] = (new Pandamp_Utility_Posts)->sanitize_post_title($attrval[$x]);
									$img['images'][$i]['slug'] = (new Pandamp_Utility_Posts)->sanitize_post_name($attrval[$x]);
								}
								else
								{
									$img['images'][$i][$attr[$x]] = $attrval[$x];
								}
							}
						}
					
						$img['images'][$i][$key]['url'] = $value->url;
						$img['images'][$i][$key]['size'] = $value->size;
					}
				}
				array_push($arrs, $img);
			}

			$arrayFields = [];
			foreach ($arrs as $arr)
			{
				if (is_array($arr)) {
					$arrayFields = array_merge($arrayFields,$arr);
				}
			}
				
			$postArticle->fields = $arrayFields;
				
			if (isset($condition)) {
				$postArticle->condition = $condition;
			}
			else
				unset($postArticle->condition);
			
			if (isset($type)) {
				$postArticle->type = $type;
			}
			
			if ($price) {
				$postArticle->price = $price;
			}
			else
				unset($postArticle->price);
				
			
			if ($location != 0) {
				$postArticle->location = $location;
			}
			else
				unset($postArticle->location);
			
			if (isset($sticky) && !empty($sticky)) {
				$postArticle->sticky = $sticky;
			}
			else
				unset($postArticle->sticky);
			
			if (isset($terms) && $terms == 'on') {
				$postArticle->terms = 'Y';
			}
			else
				unset($postArticle->terms);
			
			if (isset($allowComment) && $allowComment == 'on') {
				$postArticle->allowComment = 'Y';
			}
			else
				unset($postArticle->allowComment);
			
			if (!$content)
				unset($postArticle->content);
				
			if ($type == 'clinic')
			{
				if (!$source)
				{
					News_Models_Post::getMongoCollection()->update(
						['_id' => new MongoId($postArticle->getId())],
						['$unset' => ["source"=>$postArticle->source]]
					);
				}
				if (!$answeredby)
				{
					News_Models_Post::getMongoCollection()->update(
						['_id' => new MongoId($postArticle->getId())],
						['$unset' => ["answeredby"=>$postArticle->answeredby]]
					);
						
				}
			}
			
			News_Models_Post::update(['_id' => new MongoId($postArticle->getId())],$postArticle->export());

			$this->_helper->getHelper('FlashMessenger')
				->addMessage('Data has been updated successfully');
			$this->_redirect($this->view->serverUrl() .  $this->view->url(array('id' => $articleId), 'news_article_edit'));
		}
	}
	
	public function deleteAction()
	{
		$request = $this->getRequest();
		$id = $request->getParam('id');
		
		$criteria = ['_id' => new \MongoId($id)];
		
		News_Models_Post::remove($criteria);
		
		$this->_helper->getHelper('FlashMessenger')
			->addMessage('Data "'.$id.'" has been deleted successfully');
		$this->_redirect($this->view->serverUrl() .  $this->view->url(array('username' => $this->view->getIdentity->user_name), 'core_auth_profile'));
	}
	
	public function checkSlugAction()
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		
		$request = $this->getRequest();
		if ($request->isPost()) {
			$title = $request->getPost('title');
			$slug = (new Pandamp_Utility_Posts)->sanitize_post_name($title);
			if ($id = $request->getPost('id')) {
				$article = News_Models_Post::fetchOne([
						'_id' => ['$ne' => new \MongoId($id)],
						'slug' => $slug
					]);
				
			}
			else
				$article = News_Models_Post::fetchOne(['slug' => $slug]);
			
			if ($article) {
				$validate = 'false';
			}
			else
				$validate = 'true';
				
			
			$ret = Zend_Json::encode($validate);
			$this->getResponse()->setBody($ret);
		}
	}
	
	public function archiveAction()
	{
		$request = $this->getRequest();
		$month = $request->getParam('month');
		$year = $request->getParam('year');
		$limit = $request->getParam('limit',15);
		$page = $request->getParam('page',1);
		
		// the mktime function does not require a leading zero to the month number
		$monthName = date("F", mktime(null, null, null, $month, 1));
		
		$criteria = [];
		
		$criteria = [
			'date.create' => [
				'$gte' => new \MongoDate(strtotime($year.'-'.$month.'-01 00:00:00')),
				'$lte' => new \MongoDate(strtotime($year.'-'.$month.'-31 23:59:59')),
			]
		];
		
		$posts = News_Models_Post::all($criteria)->sort(['date.create' => -1]);
		$adapter = new Shanty_Paginator_Adapter_Mongo($posts);
		$paginator = new Zend_Paginator($adapter);
		$paginator->setCurrentPageNumber($page);
		$paginator->setItemCountPerPage($limit);
		
		$this->_helper->layout()->getArch = $year.'-'.$month;
		
		$this->view->assign('posts',$paginator);
		$this->view->assign('page', $page);
		$this->view->assign('archive', $monthName.' | '.$year);
		$this->view->assign('formatting', new Formatting());
	}
	
	public function detailAction()
	{
		$request = $this->getRequest();
		$slug = $request->getParam('slug');
		$page = $request->getParam('page',1);
		
		$perPage = 1;
		
		$config = Pandamp_Config::getConfig();
		
		$article = News_Models_Post::fetchOne(['slug' => $slug]);
		
		$title = strip_tags($article->title);
		
		//$tags = array_map('trim', explode(',', $config->web->meta->keyword));
		$tags = '';
		
		News_Models_Post::increaseViews($slug);
		
		$this->view->doctype(Zend_View_Helper_Doctype::XHTML1_RDFA);
		$this->view->headMeta()->setProperty('og:title', $title);
		$this->view->headMeta()->setProperty('twitter:card', "summary_large_image");
		
		if (isset($article->fields['tags']))
		{
			$tags = $article->fields['tags']->export();
			$keywords = implode(',',$tags);
			$this->view->headMeta()->setName('keyword', $keywords);
		}
		else
			$this->view->headMeta()->setName('keyword', $config->web->meta->keyword);
		
		if (isset($article->allowComment))
			$this->_helper->layout()->allowComment = $article->allowComment;
		
		// dipakai widget relatedarticle
		$this->_helper->layout()->articleId = $article['_id']->{'$id'};
		$this->_helper->layout()->title = $title;
		$this->_helper->layout()->tags = $tags;
		$this->_helper->layout()->category = $article->fields['category'][0]['categoryId'];
		
		$newtext = wordwrap($article->content, 3000, '#|#');
		$ar_text = explode('#|#', $newtext);
		$numPages = count($ar_text);
		
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_Null($numPages));
		$paginator->setCurrentPageNumber($page)
			->setItemCountPerPage($perPage)
			->setPageRange(3);
		
		$this->view->assign('article', $article);
		$this->view->assign('paginator', $paginator);
		
		libxml_use_internal_errors(true); // basically hide 'em, we don't care
		$dom = new DOMDocument();
		// Load with no html/body tags and do not add a default dtd
		$dom->loadHTML($ar_text[$page-1], LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
		$upost = $dom->saveHTML();
		$this->view->assign('ct', $upost);
	}
}