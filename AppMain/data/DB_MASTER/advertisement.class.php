<?php
 namespace AppMain\data\DB_MASTER;
use System\database\BaseTable;
 class advertisement extends BaseTable{
protected function initTable(){ $this->fields=[
'id'=> ['type' => 'i', 'value' => null],
'adv_name'=> ['type' => 's', 'value' => null],
'adv_img'=> ['type' => 's', 'value' => null],
'adv_url'=> ['type' => 's', 'value' => null],
'is_pc'=> ['type' => 'i', 'value' => null],
'sort_order'=> ['type' => 'i', 'value' => null],
'add_time'=> ['type' => 'i', 'value' => null],
'update_time'=> ['type' => 'i', 'value' => null],
'is_on'=> ['type' => 'i', 'value' => null],
];
$this->tableName = 'advertisement';
$this->AIField = 'id';
}
}
