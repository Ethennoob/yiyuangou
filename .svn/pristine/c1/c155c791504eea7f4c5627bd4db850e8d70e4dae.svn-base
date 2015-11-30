<?php
/**
 * 一元购系统---用户购买记录查询类
 * @authors 凌翔 (553299576@qq.com)
 * @date    2015-11-29 00:08:59
 * @version $Id$
 */
    namespace AppMain\helper;
    use System\BaseHelper;
    /*
    * 购买记录联表查询class
    */
    class PurchaseDetailHelper extends BaseHelper{
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
        public function  getPurchaseDetail($whereStmt, $bindParams = null, $bindTypes = null, $getOne = false, $order = null, $sqlFunction = null){
            $this->PurchaseDetailLinkedTable($fieldsName, $multiSqlStmt, $whereStmt, $bindParams, $bindTypes, $order, $sqlFunction);
            return $this->getMulti($multiSqlStmt, $fieldsName, $getOne);
        }
        public function getPurchaseDetailListLength($whereStmt, $bindParams = null, $bindTypes = null, $getOne = true, $order = null) {
            $this->PurchaseDetailLinkedTable($fieldsName, $multiSqlStmt, $whereStmt, $bindParams, $bindTypes, $order);
            return $this->getMultiLength($multiSqlStmt, $fieldsName, $getOne);
        }
        private function PurchaseDetailLinkedTable(&$fieldsName, &$multiSqlStmt, $whereStmt, $bindParams = null, $bindTypes = null, $orderBy = null, $sqlFunction = null) {            
            $fieldsName = array(
                    'purchase as A' => 'goods_id,thematic_id,code,add_time',
                    'goods as B' => 'goods_sn,goods_title,price,goods_thumb,free_post',
                    'thematic as C' => 'thematic_name,status'
                    
            );
            $multiSqlStmt = array(
                'joinType' => array('left join','left join'),
                'joinOn' => array('A.goods_id=B.id','A.thematic_id=C.id'),
                'whereStmt' => $whereStmt,
                'bindParams' => $bindParams,
                'bindTypes' => $bindTypes,
                'orderBy' => $orderBy,
                'sqlFunction' => $sqlFunction
            );
        }
        /*
        *获取优惠券信息 
        * @param unknown $whereStmt
        * @param string $bindParams
        * @param string $bindTypes
        * @param string $getOne
        * @param string $order
        * @param string $sqlFunction
        * @return Ambigous <NULL, unknown, multitype:, \System\database\this>
        */
        public function  getCoupon($whereStmt, $bindParams = null, $bindTypes = null, $getOne = false, $order = null, $sqlFunction = null){
            $this->CouponLinkedTable($fieldsName, $multiSqlStmt, $whereStmt, $bindParams, $bindTypes, $order, $sqlFunction);
            return $this->getMulti($multiSqlStmt, $fieldsName, $getOne);
        }
        public function getCouponListLength($whereStmt, $bindParams = null, $bindTypes = null, $getOne = true, $order = null) {
            $this->CouponLinkedTable($fieldsName, $multiSqlStmt, $whereStmt, $bindParams, $bindTypes, $order);
            return $this->getMultiLength($multiSqlStmt, $fieldsName, $getOne);
        }
        private function CouponLinkedTable(&$fieldsName, &$multiSqlStmt, $whereStmt, $bindParams = null, $bindTypes = null, $orderBy = null, $sqlFunction = null) {            
            $fieldsName = array(
                    'coupon as A' => 'id,cou_sn,is_use,is_on',
                    'coupon_cat as B' => 'id,cou_name,start_date,end_date,status',
            );
            $multiSqlStmt = array(
                'joinType' => array('left join'),
                'joinOn' => array('A.cat_id=B.id'),
                'whereStmt' => $whereStmt,
                'bindParams' => $bindParams,
                'bindTypes' => $bindTypes,
                'orderBy' => $orderBy,
                'sqlFunction' => $sqlFunction
            );
        }
    }
?>