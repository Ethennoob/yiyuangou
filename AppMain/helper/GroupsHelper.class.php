<?php
/**
 * 反向团购系统---团详情联表查询类
 * @authors 凌翔 (553299576@qq.com)
 * @date    2016-01-09 13:35:04
 * @version $Id$
 */

namespace AppMain\helper;
use System\BaseHelper;
    /*
    * 开团列表联表查询class
    */
    class GroupsHelper extends BaseHelper{
        /*
        *我的团 
        */
        public function  getMyGroups($whereStmt, $bindParams = null, $bindTypes = null, $getOne = false, $order = null, $sqlFunction = null){
            $this->MyGroupsLinkedTable($fieldsName, $multiSqlStmt, $whereStmt, $bindParams, $bindTypes, $order, $sqlFunction);
            return $this->getMulti($multiSqlStmt, $fieldsName, $getOne);
        }
        public function getMyGroupsListLength($whereStmt, $bindParams = null, $bindTypes = null, $getOne = true, $order = null) {
            $this->MyGroupsLinkedTable($fieldsName, $multiSqlStmt, $whereStmt, $bindParams, $bindTypes, $order);
            return $this->getMultiLength($multiSqlStmt, $fieldsName, $getOne);
        }
        private function MyGroupsLinkedTable(&$fieldsName, &$multiSqlStmt, $whereStmt, $bindParams = null, $bindTypes = null, $orderBy = null, $sqlFunction = null) {            
            $fieldsName = array(
                    'groupbuy_group_member as A' => 'id,user_id,group_id,add_time',
                    'groupbuy_groups as B' => 'goods_id,status',
                    'groupbuy_goods as C' => 'goods_name,price,num,goods_album',
                    
            );
            $multiSqlStmt = array(
                'joinType' => array('left join','left join'),
                'joinOn' => array('A.group_id=B.id','B.goods_id=C.id'),
                'whereStmt' => $whereStmt,
                'bindParams' => $bindParams,
                'bindTypes' => $bindTypes,
                'orderBy' => $orderBy,
                'sqlFunction' => $sqlFunction
            );
        }
        
        /*
        *团详情 
        */
        public function  getGroupsDetail($whereStmt, $bindParams = null, $bindTypes = null, $getOne = false, $order = null, $sqlFunction = null){
            $this->GroupsDetailLinkedTable($fieldsName, $multiSqlStmt, $whereStmt, $bindParams, $bindTypes, $order, $sqlFunction);
            return $this->getMulti($multiSqlStmt, $fieldsName, $getOne);
        }
        public function getGroupsDetailListLength($whereStmt, $bindParams = null, $bindTypes = null, $getOne = true, $order = null) {
            $this->GroupsDetailLinkedTable($fieldsName, $multiSqlStmt, $whereStmt, $bindParams, $bindTypes, $order);
            return $this->getMultiLength($multiSqlStmt, $fieldsName, $getOne);
        }
        private function GroupsDetailLinkedTable(&$fieldsName, &$multiSqlStmt, $whereStmt, $bindParams = null, $bindTypes = null, $orderBy = null, $sqlFunction = null) {            
            $fieldsName = array(
                    'groupbuy_groups as A' => 'id,goods_id',
                    'groupbuy_goods as B' => 'goods_name,price,market_price,num,goods_album,group_time,add_time',
                    
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