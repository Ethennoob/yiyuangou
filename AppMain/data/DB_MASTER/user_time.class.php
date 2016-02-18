<?php
namespace AppMain\data\DB_MASTER;
class user_time {
	static public function initTable(){ 
		return [
			'id'=> ['type' => 'i', 'value' => null],
			'phone'=> ['type' => 'i', 'value' => null],
			'count'=> ['type' => 'i', 'value' => null],
			'time'=> ['type' => 'i', 'value' => null],
		];
	}
	static public function AIField(){ 
		return 'id';
	}
}
