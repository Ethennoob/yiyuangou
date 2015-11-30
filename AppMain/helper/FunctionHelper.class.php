<?php
    namespace AppMain\helper;
    use System\BaseHelper;
    /*
    * 公共函数class
    */
    class  FunctionHelper extends BaseHelper{
        /*
        * 判断表中某字段是否重复，若重复则中止程序，并给出错误信息
        * @access  public
        * @param   string  $tableName    数据表名
        * @param   string  $fieldName   数据字段名
        * @param   string  $fieldValue   数据字段值
        * @return void
        */
        public function isOnlyone($tableName,$fieldName,$fieldValue){
            $manager=$this->table($tableName)->where([$fieldName=>$fieldValue])->get([$fieldName],null);
            return $manager;
        }
    }
?>