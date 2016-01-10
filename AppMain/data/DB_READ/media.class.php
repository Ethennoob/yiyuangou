<?php
namespace AppMain\data\DB_READ;
class media {
	static public function initTable(){ 
		return [
			'id'=> ['type' => 'i', 'value' => null],
			'type'=> ['type' => 'i', 'value' => null],
			'path'=> ['type' => 's', 'value' => null],
			'w_h'=> ['type' => 's', 'value' => null],
			'thumb_200_path'=> ['type' => 's', 'value' => null],
			'thumb_200_w_h'=> ['type' => 's', 'value' => null],
			'thumb_360_path'=> ['type' => 's', 'value' => null],
			'thumb_360_w_h'=> ['type' => 's', 'value' => null],
			'category'=> ['type' => 'i', 'value' => null],
			'media_id'=> ['type' => 's', 'value' => null],
			'is_admin'=> ['type' => 'i', 'value' => null],
			'add_time'=> ['type' => 'i', 'value' => null],
			'update_time'=> ['type' => 'i', 'value' => null],
			'is_on'=> ['type' => 'i', 'value' => null],
		];
	}
	static public function AIField(){ 
		return 'id';
	}
}
