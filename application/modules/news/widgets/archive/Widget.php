<?php
class News_Widgets_Archive_Widget extends Pandamp_Widget
{
	protected function _prepareShow() 
	{
		$months = 12;
		$json = [];
		for ($i=0;$i<=$months;$i++) {
			$month = date('Y-n', strtotime('-'.$i.' month'));
			$exp = explode('-', $month);
			$json[$month] = [
				'_id' => [
					'month' => (int) $exp[1],
					'year' => (int) $exp[0]
				],
				'total' => 0
			];
		}
		
		$from = strtotime(date('Y-m-30 23:59:59', strtotime('-'.$months.' month')));
		$to = time();
		
		$criteria = [
			'date.create' => [
				'$gte' => new \MongoDate($from),
				'$lte' => new \MongoDate($to),
			],
			'status' => 'publish'
		];
		
		$field = '$date.create';
		
		$agrgate = (new News_Models_Post)->getMongoCollection()->aggregate(
			['$match' => $criteria],
			[
				'$group' => [
					'_id' => [
						'month' => ['$month' => $field],
						'year' => ['$year' => $field]
					],
					'total' => ['$sum' => 1]
				]
			],
			[
				'$sort' => ['total' => -1]
			]		
		);
		
		if ($agrgate['ok'] && ! empty($agrgate['result'])) {
			$result = [];
			
			foreach ($agrgate['result'] as $res){
				$result[$res['_id']['year'].'-'.$res['_id']['month']] = $res;
			}
			$json = array_merge($json, $result);
			
			$this->_view->assign('archives',array_values($json));
		}
	}
}
