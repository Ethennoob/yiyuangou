<?php
namespace AppMain\data\DB_READ;
class roll {
	static public function initTable(){ 
		return [
			'id'=> ['type' => 'i', 'value' => null],
			'goods_id'=> ['type' => 'i', 'value' => null],
			'is_on'=> ['type' => 'i', 'value' => null],
		];
	}
	static public function AIField(){ 
		return 'id';
	}
}
