<?php
namespace AppMain\data\DB_MASTER;
class groupbuy_goods {
	static public function initTable(){ 
		return [
			'id'=> ['type' => 'i', 'value' => null],
			'goods_name'=> ['type' => 's', 'value' => null],
			'goods_title'=> ['type' => 's', 'value' => null],
			'num'=> ['type' => 'i', 'value' => null],
			'group_time'=> ['type' => 'i', 'value' => null],
			'price'=> ['type' => 'd', 'value' => null],
			'cost_price'=> ['type' => 'd', 'value' => null],
			'market_price'=> ['type' => 'd', 'value' => null],
			'goods_album'=> ['type' => 's', 'value' => null],
			'discount_rate'=> ['type' => 'd', 'value' => null],
			'is_show'=> ['type' => 'i', 'value' => null],
			'stock'=> ['type' => 'i', 'value' => null],
			'add_time'=> ['type' => 'i', 'value' => null],
			'update_time'=> ['type' => 'i', 'value' => null],
			'is_on'=> ['type' => 'i', 'value' => null],
		];
	}
	static public function AIField(){ 
		return 'id';
	}
}
