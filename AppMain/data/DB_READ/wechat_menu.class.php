<?php
namespace AppMain\data\DB_READ;
class wechat_menu {
	static public function initTable(){ 
		return [
			'id'=> ['type' => 'i', 'value' => null],
			'url'=> ['type' => 's', 'value' => null],
			'keyword'=> ['type' => 's', 'value' => null],
			'title'=> ['type' => 's', 'value' => null],
			'pid'=> ['type' => 'i', 'value' => null],
			'sort'=> ['type' => 'i', 'value' => null],
			'add_time'=> ['type' => 'i', 'value' => null],
			'update_time'=> ['type' => 'i', 'value' => null],
			'is_on'=> ['type' => 'i', 'value' => null],
		];
	}
	static public function AIField(){ 
		return 'id';
	}
}
