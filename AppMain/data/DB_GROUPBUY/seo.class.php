<?php
namespace AppMain\data\DB_GROUPBUY;
class seo {
	static public function initTable(){ 
		return [
			'id'=> ['type' => 'i', 'value' => null],
			'cid'=> ['type' => 'i', 'value' => null],
			'keywords'=> ['type' => 's', 'value' => null],
			'description'=> ['type' => 's', 'value' => null],
		];
	}
	static public function AIField(){ 
		return 'id';
	}
}
