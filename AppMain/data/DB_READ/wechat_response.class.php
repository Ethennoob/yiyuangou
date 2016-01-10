<?php
namespace AppMain\data\DB_READ;
class wechat_response {
	static public function initTable(){ 
		return [
			'id'=> ['type' => 'i', 'value' => null],
			'type'=> ['type' => 'i', 'value' => null],
			'rsp_type'=> ['type' => 'i', 'value' => null],
			'keywords'=> ['type' => 's', 'value' => null],
			'text'=> ['type' => 's', 'value' => null],
			'news'=> ['type' => 's', 'value' => null],
			'add_time'=> ['type' => 'i', 'value' => null],
			'update_time'=> ['type' => 'i', 'value' => null],
			'is_on'=> ['type' => 'i', 'value' => null],
		];
	}
	static public function AIField(){ 
		return 'id';
	}
}
