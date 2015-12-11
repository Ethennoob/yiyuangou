<?php
 namespace AppMain\data\DB_MASTER;
use System\database\BaseTable;
 class order extends BaseTable{
protected function initTable(){ $this->fields=[
];
$this->tableName = 'order';
$this->AIField = '';
}
}
