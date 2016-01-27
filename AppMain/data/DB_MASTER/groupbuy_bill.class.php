<?php
namespace AppMain\data\DB_MASTER;
class groupbuy_bill {
	static public function initTable(){ 
		return [
			'id'=> ['type' => 'i', 'value' => null],
			'bill_sn'=> ['type' => 's', 'value' => null],
			'logistics_num'=> ['type' => 's', 'value' => null],
			'group_id'=> ['type' => 'i', 'value' => null],
			'address_id'=> ['type' => 'i', 'value' => null],
			'user_id'=> ['type' => 'i', 'value' => null],
			'goods_id'=> ['type' => 'i', 'value' => null],
			'status'=> ['type' => 'i', 'value' => null],
			'type'=> ['type' => 'i', 'value' => null],
			'add_time'=> ['type' => 'i', 'value' => null],
			'pay_time'=> ['type' => 'i', 'value' => null],
			'post_time'=> ['type' => 'i', 'value' => null],
			'done_time'=> ['type' => 'i', 'value' => null],
			'update_time'=> ['type' => 'i', 'value' => null],
			'is_on'=> ['type' => 'i', 'value' => null],
		];
	}
	static public function AIField(){ 
		return 'id';
	}
}
