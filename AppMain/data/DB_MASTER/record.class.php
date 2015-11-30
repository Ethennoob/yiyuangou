<?php
 namespace AppMain\data\DB_MASTER;
use System\database\BaseTable;
 class record extends BaseTable{
protected function initTable(){ $this->fields=[
'id'=> ['type' => 'i', 'value' => null],
'goods_id'=> ['type' => 'i', 'value' => null],
'user_id'=> ['type' => 'i', 'value' => null],
'thematic_id'=> ['type' => 'i', 'value' => null],
'num'=> ['type' => 'i', 'value' => null],
'add_time'=> ['type' => 'i', 'value' => null],
'update_time'=> ['type' => 'i', 'value' => null],
'is_on'=> ['type' => 'i', 'value' => null],
];
$this->tableName = 'record';
$this->AIField = 'id';
}
}
