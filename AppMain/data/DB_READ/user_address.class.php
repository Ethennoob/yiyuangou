<?php
namespace AppMain\data\DB_READ;
class user_address {
	static public function initTable(){ 
		return [
			'id'=> ['type' => 'i', 'value' => null],
			'user_id'=> ['type' => 'i', 'value' => null],
			'province'=> ['type' => 's', 'value' => null],
			'city'=> ['type' => 's', 'value' => null],
			'area'=> ['type' => 's', 'value' => null],
			'street'=> ['type' => 's', 'value' => null],
			'postcode'=> ['type' => 'i', 'value' => null],
			'is_default'=> ['type' => 'i', 'value' => null],
			'mobile'=> ['type' => 'i', 'value' => null],
			'name'=> ['type' => 's', 'value' => null],
			'add_time'=> ['type' => 'i', 'value' => null],
			'update_time'=> ['type' => 'i', 'value' => null],
			'is_on'=> ['type' => 'i', 'value' => null],
		];
	}
	static public function AIField(){ 
		return 'id';
	}
}
