<?php
/**
 * 一元购系统---用户信息查询类
 * @authors 凌翔 (553299576@qq.com)
 * @date    2015-11-29 15:45:20
 * @version $Id$
 */

namespace AppMain\helper;
use System\BaseHelper;
    /*
    * 订单商品信息联表查询class
    */
    class UserInfoHelper extends BaseHelper{
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
        public function  getUserInfo($whereStmt, $bindParams = null, $bindTypes = null, $getOne = true, $order = null, $sqlFunction = null){
            $this->UserInfoLinkedTable($fieldsName, $multiSqlStmt, $whereStmt, $bindParams, $bindTypes, $order, $sqlFunction);
            return $this->getMulti($multiSqlStmt, $fieldsName, $getOne);
        }
        public function getUserInfoListLength($whereStmt, $bindParams = null, $bindTypes = null, $getOne = true, $order = null) {
            $this->UserInfoLinkedTable($fieldsName, $multiSqlStmt, $whereStmt, $bindParams, $bindTypes, $order);
            return $this->getMultiLength($multiSqlStmt, $fieldsName, $getOne);
        }
        private function UserInfoLinkedTable(&$fieldsName, &$multiSqlStmt, $whereStmt, $bindParams = null, $bindTypes = null, $orderBy = null, $sqlFunction = null) {            
            $fieldsName = array(
                    'user as A' => 'id,phone,openid,user_img,last_login,last_ip,nickname,is_follow,is_froze,add_time,update_time',
                    'user_address as B' => 'province,city,area,street,is_default,mobile,name',
                   
                    
            );
            $multiSqlStmt = array(
                'joinType' => array('left join'),
                'joinOn' => array('A.id=B.user_id'),
                'whereStmt' => $whereStmt,
                'bindParams' => $bindParams,
                'bindTypes' => $bindTypes,
                'orderBy' => $orderBy,
                'sqlFunction' => $sqlFunction
            );
        }
    }