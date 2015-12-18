<?php
namespace AppMain\data\DB_MASTER;
class wechat_news {
	static public function initTable(){ 
		return [
			'id'=> ['type' => 'i', 'value' => null],
			'title'=> ['type' => 's', 'value' => null],
			'author'=> ['type' => 's', 'value' => null],
			'content'=> ['type' => 's', 'value' => null],
			'img'=> ['type' => 's', 'value' => null],
			'img_thumb'=> ['type' => 's', 'value' => null],
			'url'=> ['type' => 's', 'value' => null],
			'desc'=> ['type' => 's', 'value' => null],
			'sort'=> ['type' => 'i', 'value' => null],
			'add_time'=> ['type' => 'i', 'value' => null],
			'update_time'=> ['type' => 'i', 'value' => null],
			'is_on'=> ['type' => 'i', 'value' => null],
		];
	}
	static public function AIField(){ 
		return 'id';
	}
}
