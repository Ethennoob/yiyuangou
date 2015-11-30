<?php
 namespace AppMain\data\DB_MASTER;
use System\database\BaseTable;
 class article_cat extends BaseTable{
protected function initTable(){ $this->fields=[
'id'=> ['type' => 'i', 'value' => null],
'cat_name'=> ['type' => 's', 'value' => null],
'cat_type'=> ['type' => 'i', 'value' => null],
'cat_desc'=> ['type' => 's', 'value' => null],
'sort_order'=> ['type' => 'i', 'value' => null],
'is_show'=> ['type' => 'i', 'value' => null],
'is_on'=> ['type' => 'i', 'value' => null],
'add_time'=> ['type' => 'i', 'value' => null],
'update_time'=> ['type' => 'i', 'value' => null],
];
$this->tableName = 'article_cat';
$this->AIField = 'id';
}
}
