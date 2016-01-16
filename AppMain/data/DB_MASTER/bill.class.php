<?php
namespace AppMain\data\DB_MASTER;
class bill {
	static public function initTable(){ 
		return [
			'id'=> ['type' => 'i', 'value' => null],
			'user_id'=> ['type' => 'i', 'value' => null],
			'address_id'=> ['type' => 'i', 'value' => null],
			'thematic_id'=> ['type' => 'i', 'value' => null],
			'record_id'=> ['type' => 'i', 'value' => null],
			'company_id'=> ['type' => 'i', 'value' => null],
			'goods_id'=> ['type' => 'i', 'value' => null],
			'bill_sn'=> ['type' => 's', 'value' => null],
			'code'=> ['type' => 's', 'value' => null],
			'status'=> ['type' => 'i', 'value' => null],
			'is_confirm'=> ['type' => 'i', 'value' => null],
			'is_post'=> ['type' => 'i', 'value' => null],
			'is_cancel'=> ['type' => 'i', 'value' => null],
			'add_time'=> ['type' => 'i', 'value' => null],
			'update_time'=> ['type' => 'i', 'value' => null],
			'is_on'=> ['type' => 'i', 'value' => null],
		];
	}
	static public function AIField(){ 
		return 'id';
	}
}
