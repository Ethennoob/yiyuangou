<?php
/**
 * 反向团购系统---开团列表联表类
 * @authors 凌翔 (553299576@qq.com)
 * @date    2016-01-07 16:19:36
 * @version $Id$
 */

namespace AppMain\helper;
use System\BaseHelper;
    /*
    * 开团列表联表查询class
    */
    class GroupListHelper extends BaseHelper{
        /*
        *获取活动信息 
        * @param unknown $whereStmt
        * @param string $bindParams
        * @param string $bindTypes
        * @param string $getOne
        * @param string $order
        * @param string $sqlFunction
        * @return Ambigous <NULL, unknown, multitype:, \System\database\this>
        */
        public function  getGroupList($whereStmt, $bindParams = null, $bindTypes = null, $getOne = false, $order = null, $sqlFunction = null){
            $this->GroupListLinkedTable($fieldsName, $multiSqlStmt, $whereStmt, $bindParams, $bindTypes, $order, $sqlFunction);
            return $this->getMulti($multiSqlStmt, $fieldsName, $getOne);
        }
        public function getGroupListListLength($whereStmt, $bindParams = null, $bindTypes = null, $getOne = true, $order = null) {
            $this->GroupListLinkedTable($fieldsName, $multiSqlStmt, $whereStmt, $bindParams, $bindTypes, $order);
            return $this->getMultiLength($multiSqlStmt, $fieldsName, $getOne);
        }
        private function GroupListLinkedTable(&$fieldsName, &$multiSqlStmt, $whereStmt, $bindParams = null, $bindTypes = null, $orderBy = null, $sqlFunction = null) {            
            $fieldsName = array(
                    'groupbuy_groups as A' => 'id,goods_id,leader,soft,stool,status,add_time',
                    'groupbuy_goods as B' => 'goods_name,price,num',
                    
            );
            $multiSqlStmt = array(
                'joinType' => array('left join'),
                'joinOn' => array('A.goods_id=B.id'),
                'whereStmt' => $whereStmt,
                'bindParams' => $bindParams,
                'bindTypes' => $bindTypes,
                'orderBy' => $orderBy,
                'sqlFunction' => $sqlFunction
            );
        }
    }