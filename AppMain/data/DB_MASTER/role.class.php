<?php
 namespace AppMain\data\DB_MASTER;
use System\database\BaseTable;
 class role extends BaseTable{
protected function initTable(){ $this->fields=[
'id'=> ['type' => 'i', 'value' => null],
'role_name'=> ['type' => 's', 'value' => null],
'role_status'=> ['type' => 'i', 'value' => null],
'role_operation'=> ['type' => 'i', 'value' => null],
'role_remark'=> ['type' => 's', 'value' => null],
'add_time'=> ['type' => 'i', 'value' => null],
'update_time'=> ['type' => 'i', 'value' => null],
'is_on'=> ['type' => 'i', 'value' => null],
];
$this->tableName = 'role';
$this->AIField = 'id';
}
}
