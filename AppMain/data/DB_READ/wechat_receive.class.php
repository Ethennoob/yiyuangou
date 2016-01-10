<?php
namespace AppMain\data\DB_READ;
class wechat_receive {
	static public function initTable(){ 
		return [
			'id'=> ['type' => 'i', 'value' => null],
			'msg_style'=> ['type' => 'i', 'value' => null],
			'to_name'=> ['type' => 's', 'value' => null],
			'from_name'=> ['type' => 's', 'value' => null],
			'create_time'=> ['type' => 'i', 'value' => null],
			'msg_type'=> ['type' => 's', 'value' => null],
			'content'=> ['type' => 's', 'value' => null],
			'pic_url'=> ['type' => 's', 'value' => null],
			'location_x'=> ['type' => 'd', 'value' => null],
			'location_y'=> ['type' => 'd', 'value' => null],
			'scale'=> ['type' => 'i', 'value' => null],
			'lable'=> ['type' => 's', 'value' => null],
			'title'=> ['type' => 's', 'value' => null],
			'description'=> ['type' => 's', 'value' => null],
			'url'=> ['type' => 's', 'value' => null],
			'event'=> ['type' => 's', 'value' => null],
			'event_key'=> ['type' => 's', 'value' => null],
			'ticket'=> ['type' => 's', 'value' => null],
			'msg_id'=> ['type' => 'i', 'value' => null],
			'scene_id'=> ['type' => 's', 'value' => null],
			'kf_account'=> ['type' => 's', 'value' => null],
		];
	}
	static public function AIField(){ 
		return 'id';
	}
}
