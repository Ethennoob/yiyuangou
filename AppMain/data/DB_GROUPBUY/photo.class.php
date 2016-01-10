<?php
namespace AppMain\data\DB_GROUPBUY;
class photo {
	static public function initTable(){ 
		return [
			'id'=> ['type' => 'i', 'value' => null],
			'type'=> ['type' => 's', 'value' => null],
			'binarydata'=> ['type' => 'i', 'value' => null],
			'cid'=> ['type' => 'i', 'value' => null],
			'title'=> ['type' => 's', 'value' => null],
		];
	}
	static public function AIField(){ 
		return 'id';
	}
}
