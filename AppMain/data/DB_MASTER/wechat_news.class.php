<?php
 namespace AppMain\data\DB_MASTER;
use System\database\BaseTable;
 class wechat_news extends BaseTable{
protected function initTable(){ $this->fields=[
'id'=> ['type' => 'i', 'value' => null],
'title'=> ['type' => 's', 'value' => null],
'author'=> ['type' => 's', 'value' => null],
'content'=> ['type' => 's', 'value' => null],
'img'=> ['type' => 's', 'value' => null],
'img_thumb'=> ['type' => 's', 'value' => null],
'url'=> ['type' => 's', 'value' => null],
'desc'=> ['type' => 's', 'value' => null],
'sort'=> ['type' => 'i', 'value' => null],
'add_time'=> ['type' => 'i', 'value' => null],
'update_time'=> ['type' => 'i', 'value' => null],
'is_on'=> ['type' => 'i', 'value' => null],
];
$this->tableName = 'wechat_news';
$this->AIField = 'id';
}
}
