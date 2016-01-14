<?php
/**
 * 一元购系统---抽奖helper类
 * @authors 凌翔 (553299576@qq.com)
 * @date    2015-11-29 15:45:20
 * @version $Id$
 */

namespace AppMain\helper;
use System\BaseHelper;
    /*
    * 抽奖时间
    */
    class RollHelper extends BaseHelper{
        
        public function rollTime($time){
                    $livedate = date('H:i:s',$time);
                    $livedateArr = explode(':', $livedate);
                    $livetemp = implode('',$livedateArr);
                    $hour = $livedateArr[0];
                    $min = $livedateArr[1];
                    $a =str_split($min);
                    if (10<=$hour and $hour<22) {//判定是否在10:00-22:00
                        $next = 10 - $a[1];
                        $rolltime = $next*60; 
                    }elseif (22<=$hour or $hour<2) {//判定是否在22:00-2:00
                        if ($a[1]>5) {
                            $next = 10 - $a[1];
                        }else{
                            $next = 5 - $a[1];
                        }
                        $rolltime = $next*60;
                    }elseif (2<=$hour and $hour<10) {//判定是否在2:00-10:00
                        $rolltime = 0;
                    }
                    return $time+$rolltime+180;//加上需要倒数的时间;
        }
        /**
         * 50条roll_record联表查询数据class
         * @param  [type]  $whereStmt   [description]
         * @param  [type]  $bindParams  [description]
         * @param  [type]  $bindTypes   [description]
         * @param  boolean $getOne      [description]
         * @param  [type]  $order       [description]
         * @param  [type]  $sqlFunction [description]
         * @return [type]               [description]
         */
        public function  getRollRecord($whereStmt, $bindParams = null, $bindTypes = null, $getOne = false, $order = null, $sqlFunction = null){
            $this->RollRecordLinkedTable($fieldsName, $multiSqlStmt, $whereStmt, $bindParams, $bindTypes, $order, $sqlFunction);
            return $this->getMulti($multiSqlStmt, $fieldsName, $getOne);
        }
        public function getRollRecordListLength($whereStmt, $bindParams = null, $bindTypes = null, $getOne = true, $order = null) {
            $this->RollRecordLinkedTable($fieldsName, $multiSqlStmt, $whereStmt, $bindParams, $bindTypes, $order);
            return $this->getMultiLength($multiSqlStmt, $fieldsName, $getOne);
        }
        private function RollRecordLinkedTable(&$fieldsName, &$multiSqlStmt, $whereStmt, $bindParams = null, $bindTypes = null, $orderBy = null, $sqlFunction = null) {            
            $fieldsName = array(
                    'roll_record as A' => 'id,goods_id,user_id,time,shishicai,B,ms_time',
                    'user as B' => 'nickname',
                    
            );
            $multiSqlStmt = array(
                'joinType' => array('left join'),
                'joinOn' => array('A.user_id=B.id'),
                'whereStmt' => $whereStmt,
                'bindParams' => $bindParams,
                'bindTypes' => $bindTypes,
                'orderBy' => $orderBy,
                'sqlFunction' => $sqlFunction
            );
        }
       
    }