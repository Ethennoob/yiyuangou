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
    class BillHelper extends BaseHelper{
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
        public function  getBillList($whereStmt, $bindParams = null, $bindTypes = null, $getOne = false, $order = null, $sqlFunction = null){
            $this->BillListLinkedTable($fieldsName, $multiSqlStmt, $whereStmt, $bindParams, $bindTypes, $order, $sqlFunction);
            return $this->getMulti($multiSqlStmt, $fieldsName, $getOne);
        }
        public function getBillListListLength($whereStmt, $bindParams = null, $bindTypes = null, $getOne = true, $order = null) {
            $this->BillListLinkedTable($fieldsName, $multiSqlStmt, $whereStmt, $bindParams, $bindTypes, $order);
            return $this->getMultiLength($multiSqlStmt, $fieldsName, $getOne);
        }
        private function BillListLinkedTable(&$fieldsName, &$multiSqlStmt, $whereStmt, $bindParams = null, $bindTypes = null, $orderBy = null, $sqlFunction = null) {            
            $fieldsName = array(
                    'groupbuy_bill as A' => 'id,logistics_num,goods_id,bill_sn,group_id,user_id,address_id,status,add_time,
                    pay_time,post_time,done_time',
                    'groupbuy_goods as B' => 'goods_name,price,goods_album',
                    'user as C' => 'nickname',
                    'user_address as D' => 'name,mobile,province,city,area,street'
                    
            );
            $multiSqlStmt = array(
                'joinType' => array('left join','left join','left join'),
                'joinOn' => array('A.goods_id=B.id','A.user_id=C.id','A.address_id=D.id'),
                'whereStmt' => $whereStmt,
                'bindParams' => $bindParams,
                'bindTypes' => $bindTypes,
                'orderBy' => $orderBy,
                'sqlFunction' => $sqlFunction
            );
        }
    }