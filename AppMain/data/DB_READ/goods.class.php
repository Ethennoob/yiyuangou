<?php
namespace AppMain\data\DB_READ;
class goods {
	static public function initTable(){ 
		return [
			'id'=> ['type' => 'i', 'value' => null],
			'goods_sn'=> ['type' => 's', 'value' => null],
			'thematic_id'=> ['type' => 'i', 'value' => null],
			'goods_name'=> ['type' => 's', 'value' => null],
			'goods_title'=> ['type' => 's', 'value' => null],
			'upload_date'=> ['type' => 'i', 'value' => null],
			'cost_price'=> ['type' => 'i', 'value' => null],
			'price'=> ['type' => 'i', 'value' => null],
			'nature'=> ['type' => 'i', 'value' => null],
			'limit_num'=> ['type' => 'i', 'value' => null],
			'goods_desc'=> ['type' => 's', 'value' => null],
			'goods_album'=> ['type' => 's', 'value' => null],
			'goods_thumb'=> ['type' => 's', 'value' => null],
			'goods_img'=> ['type' => 's', 'value' => null],
			'free_post'=> ['type' => 'i', 'value' => null],
			'is_show'=> ['type' => 'i', 'value' => null],
			'add_time'=> ['type' => 'i', 'value' => null],
			'update_time'=> ['type' => 'i', 'value' => null],
			'is_on'=> ['type' => 'i', 'value' => null],
		];
	}
	static public function AIField(){ 
		return 'id';
	}
}
