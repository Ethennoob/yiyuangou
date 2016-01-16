<?php
namespace AppMain\data\DB_MASTER;
class logistics {
	static public function initTable(){ 
		return [
			'id'=> ['type' => 'i', 'value' => null],
			'company_id'=> ['type' => 'i', 'value' => null],
			'logistics_number'=> ['type' => 's', 'value' => null],
			'logistics_name'=> ['type' => 's', 'value' => null],
			'logistics_status'=> ['type' => 'i', 'value' => null],
			'bill_id'=> ['type' => 'i', 'value' => null],
			'user_address_id'=> ['type' => 'i', 'value' => null],
			'add_time'=> ['type' => 'i', 'value' => null],
			'update_time'=> ['type' => 'i', 'value' => null],
			'is_on'=> ['type' => 'i', 'value' => null],
		];
	}
	static public function AIField(){ 
		return 'id';
	}
}
