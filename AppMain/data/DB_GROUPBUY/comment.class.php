<?php
namespace AppMain\data\DB_GROUPBUY;
class comment {
	static public function initTable(){ 
		return [
			'id'=> ['type' => 'i', 'value' => null],
			'username'=> ['type' => 's', 'value' => null],
			'content'=> ['type' => 's', 'value' => null],
			'timeline'=> ['type' => 'i', 'value' => null],
			'tid'=> ['type' => 'i', 'value' => null],
			'cid'=> ['type' => 'i', 'value' => null],
		];
	}
	static public function AIField(){ 
		return 'id';
	}
}
