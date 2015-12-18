<?php
namespace AppMain\data\DB_MASTER;
class user {
	static public function initTable(){ 
		return [
			'id'=> ['type' => 'i', 'value' => null],
			'phone'=> ['type' => 'i', 'value' => null],
			'openid'=> ['type' => 's', 'value' => null],
			'user_img'=> ['type' => 's', 'value' => null],
			'area'=> ['type' => 's', 'value' => null],
			'last_login'=> ['type' => 'i', 'value' => null],
			'last_ip'=> ['type' => 'i', 'value' => null],
			'nickname'=> ['type' => 's', 'value' => null],
			'is_follow'=> ['type' => 'i', 'value' => null],
			'is_froze'=> ['type' => 'i', 'value' => null],
			'add_time'=> ['type' => 'i', 'value' => null],
			'update_time'=> ['type' => 'i', 'value' => null],
			'is_on'=> ['type' => 'i', 'value' => null],
		];
	}
	static public function AIField(){ 
		return 'id';
	}
}
