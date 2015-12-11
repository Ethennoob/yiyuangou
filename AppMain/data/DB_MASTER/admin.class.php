<?php
 namespace AppMain\data\DB_MASTER;
use System\database\BaseTable;
 class admin extends BaseTable{
protected function initTable(){ $this->fields=[
'id'=> ['type' => 'i', 'value' => null],
'admin_name'=> ['type' => 's', 'value' => null],
'admin_email'=> ['type' => 's', 'value' => null],
'admin_phone'=> ['type' => 's', 'value' => null],
'admin_pwd'=> ['type' => 's', 'value' => null],
'admin_level'=> ['type' => 'i', 'value' => null],
'add_time'=> ['type' => 'i', 'value' => null],
'update_time'=> ['type' => 'i', 'value' => null],
'is_on'=> ['type' => 'i', 'value' => null],
];
$this->tableName = 'admin';
$this->AIField = 'id';
}
}
