<?php
 namespace AppMain\data\DB_MASTER;
use System\database\BaseTable;
 class user extends BaseTable{
protected function initTable(){ $this->fields=[
'id'=> ['type' => 'i', 'value' => null],
'phone'=> ['type' => 'i', 'value' => null],
'openid'=> ['type' => 's', 'value' => null],
'user_img'=> ['type' => 's', 'value' => null],
'rev_name'=> ['type' => 's', 'value' => null],
'rev_phone'=> ['type' => 'i', 'value' => null],
'area'=> ['type' => 's', 'value' => null],
'address'=> ['type' => 's', 'value' => null],
'zip_code'=> ['type' => 'i', 'value' => null],
'last_login'=> ['type' => 'i', 'value' => null],
'last_ip'=> ['type' => 'i', 'value' => null],
'nickname'=> ['type' => 's', 'value' => null],
'is_follow'=> ['type' => 'i', 'value' => null],
'is_froze'=> ['type' => 'i', 'value' => null],
'add_time'=> ['type' => 'i', 'value' => null],
'update_time'=> ['type' => 'i', 'value' => null],
'is_on'=> ['type' => 'i', 'value' => null],
];
$this->tableName = 'user';
$this->AIField = 'id';
}
}
