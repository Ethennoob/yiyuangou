<?php
namespace AppMain\data\DB_MASTER;
class company {
	static public function initTable(){ 
		return [
			'id'=> ['type' => 'i', 'value' => null],
			'company_name'=> ['type' => 's', 'value' => null],
			'QR_code'=> ['type' => 's', 'value' => null],
			'add_time'=> ['type' => 'i', 'value' => null],
			'update_time'=> ['type' => 'i', 'value' => null],
			'is_on'=> ['type' => 'i', 'value' => null],
		];
	}
	static public function AIField(){ 
		return 'id';
	}
}
