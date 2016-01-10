<?php
namespace AppMain\data\DB_READ;
class wechat {
	static public function initTable(){ 
		return [
			'id'=> ['type' => 'i', 'value' => null],
			'msg_style'=> ['type' => 'i', 'value' => null],
			'from_name'=> ['type' => 's', 'value' => null],
			'to_name'=> ['type' => 's', 'value' => null],
			'create_time'=> ['type' => 'i', 'value' => null],
			'area'=> ['type' => 's', 'value' => null],
			'msg_id'=> ['type' => 'i', 'value' => null],
		];
	}
	static public function AIField(){ 
		return 'id';
	}
}
