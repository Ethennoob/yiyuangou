<?php
namespace AppMain\data\DB_GROUPBUY;
class article {
	static public function initTable(){ 
		return [
			'id'=> ['type' => 'i', 'value' => null],
			'cid'=> ['type' => 'i', 'value' => null],
			'type'=> ['type' => 's', 'value' => null],
			'title'=> ['type' => 's', 'value' => null],
			'content'=> ['type' => 's', 'value' => null],
			'timeline'=> ['type' => 'i', 'value' => null],
			'img'=> ['type' => 'i', 'value' => null],
		];
	}
	static public function AIField(){ 
		return 'id';
	}
}
