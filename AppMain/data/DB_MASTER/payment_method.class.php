<?php
 namespace AppMain\data\DB_MASTER;
use System\database\BaseTable;
 class payment_method extends BaseTable{
protected function initTable(){ $this->fields=[
'id'=> ['type' => 'i', 'value' => null],
'pay_name'=> ['type' => 's', 'value' => null],
'pay_desc'=> ['type' => 's', 'value' => null],
'pay_mode'=> ['type' => 's', 'value' => null],
'pay_icon'=> ['type' => 's', 'value' => null],
'add_time'=> ['type' => 'i', 'value' => null],
'update_time'=> ['type' => 'i', 'value' => null],
'is_on'=> ['type' => 'i', 'value' => null],
];
$this->tableName = 'payment_method';
$this->AIField = 'id';
}
}
