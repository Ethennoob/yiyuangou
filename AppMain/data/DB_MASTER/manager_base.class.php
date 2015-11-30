<?php
 namespace AppMain\data\DB_MASTER;
use System\database\BaseTable;
 class manager_base extends BaseTable{
protected function initTable(){ $this->fields=[
'id'=> ['type' => 'i', 'value' => null],
'manager_name'=> ['type' => 's', 'value' => null],
'manager_email'=> ['type' => 's', 'value' => null],
'manager_phone'=> ['type' => 's', 'value' => null],
'manager_password'=> ['type' => 's', 'value' => null],
'manager_level'=> ['type' => 's', 'value' => null],
'role_base_id'=> ['type' => 'i', 'value' => null],
'manager_endlogin'=> ['type' => 'i', 'value' => null],
'add_time'=> ['type' => 'i', 'value' => null],
'update_time'=> ['type' => 'i', 'value' => null],
'is_on'=> ['type' => 'i', 'value' => null],
];
$this->tableName = 'manager_base';
$this->AIField = 'id';
}
}
