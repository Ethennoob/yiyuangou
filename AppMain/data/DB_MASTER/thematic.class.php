<?php
 namespace AppMain\data\DB_MASTER;
use System\database\BaseTable;
 class thematic extends BaseTable{
protected function initTable(){ $this->fields=[
'id'=> ['type' => 'i', 'value' => null],
'thematic_name'=> ['type' => 's', 'value' => null],
'nature'=> ['type' => 'i', 'value' => null],
'status'=> ['type' => 'i', 'value' => null],
'poster'=> ['type' => 's', 'value' => null],
'is_show'=> ['type' => 'i', 'value' => null],
'add_time'=> ['type' => 'i', 'value' => null],
'update_time'=> ['type' => 'i', 'value' => null],
'is_on'=> ['type' => 'i', 'value' => null],
];
$this->tableName = 'thematic';
$this->AIField = 'id';
}
}
