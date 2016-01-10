<?php
namespace AppMain\data\DB_MASTER;
class advertisement {
	static public function initTable(){ 
		return [
			'id'=> ['type' => 'i', 'value' => null],
			'adv_name'=> ['type' => 's', 'value' => null],
			'adv_img'=> ['type' => 's', 'value' => null],
			'adv_url'=> ['type' => 's', 'value' => null],
			'sort_order'=> ['type' => 'i', 'value' => null],
			'add_time'=> ['type' => 'i', 'value' => null],
			'update_time'=> ['type' => 'i', 'value' => null],
			'is_on'=> ['type' => 'i', 'value' => null],
		];
	}
	static public function AIField(){ 
		return 'id';
	}
}
