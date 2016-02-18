<?php
/**
 * 反向团购系统---订单管理类
 * @authors 凌翔 (553299576@qq.com)
 * @date    2016-01-07 17:34:35
 * @version $Id$
 */

namespace AppMain\controller\Groupbuy;
use \System\BaseClass;

class BackendBillController extends BaseClass {
    /**
     * 订单列表
     */
    public function billList(){
    	$pageInfo = $this->P();
        //调用Helper类
        $dataClass=$this->H('Bill');
        if (isset($_POST['goods_id'])) {
            $this->V(['goods_id'=>['egNum']]);
            $goods_id = intval($_POST['goods_id']);
            $good = $this->table('groupbuy_goods')->where(['is_on'=>1,'id'=>$goods_id])->get(['id'],true);
            if (!$good) {
                $this->R('',70009);
            } 
            $where = 'A.is_on = 1 and A.goods_id='.$goods_id;
        }elseif(isset($_POST['group_id'])){
            $this->V(['group_id'=>['egNum']]);
            $group_id = intval($_POST['group_id']);
            $group = $this->table('groupbuy_groups')->where(['is_on'=>1,'id'=>$group_id])->get(['id'],true);
            if (!$group) {
                $this->R('',70009);
            } 
            $where = 'A.is_on = 1 and A.group_id='.$group_id;
        }else{
            $where = "A.is_on = 1";
        }
        $order='A.id desc';
        $billList=$dataClass->getbillList(null,null,null,false,$order);
        $billList=$this->getOnePageData($pageInfo, $dataClass, 'getBillList','getBillListListLength',[$where,null,null,false,$order],true);
        if($billList){
                foreach ($billList as $k=>$v){
                    $billList[$k]['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
                    $billList[$k]['pay_time'] = date('Y-m-d H:i:s',$v['pay_time']);
                    $billList[$k]['post_time'] = date('Y-m-d H:i:s',$v['post_time']);
                    $billList[$k]['done_time'] = date('Y-m-d H:i:s',$v['done_time']);
                    unset($billList[$k]['goods_album']);
                }
        }else{
            $billList=null;
        }
        $this->R(['billList'=>$billList,'page'=>$pageInfo]);
    }
    /**
     * 查询单条订单
     */
    public function billOneDetail(){
        $this->V(['bill_id'=>['egNum']]);
        $id = intval($_POST['bill_id']);
        //调用Helper类
        $dataClass=$this->H('Bill');
        $where = 'A.is_on = 1 and A.id='.$id;
        $order='A.id desc';
        $billOneDetail=$dataClass->getbillList($where,null,null,true,$order);
        if ($billOneDetail) {
        $billOneDetail['add_time'] = date('Y-m-d H:i:s',$billOneDetail['add_time']);
        $billOneDetail['pay_time'] = date('Y-m-d H:i:s',$billOneDetail['pay_time']);
        $billOneDetail['post_time'] = date('Y-m-d H:i:s',$billOneDetail['post_time']);
        $billOneDetail['done_time'] = date('Y-m-d H:i:s',$billOneDetail['done_time']);
        $status = $this->table('user')->where(['is_on'=>1,'id'=>$billOneDetail['user_id']])->get(['nickname'],true);
        $billOneDetail['nickname'] = $status['nickname'];
        $status = $this->table('user_address')->where(['is_on'=>1,'is_default'=>1,'id'=>$billOneDetail['address_id']])->get(['name','mobile','province','city','area','street'],true);
        $billOneDetail['name'] = $status['name'];
        $billOneDetail['mobile'] = $status['mobile'];
        $billOneDetail['province'] = $status['province'];
        $billOneDetail['city'] = $status['city'];
        $billOneDetail['area'] = $status['area'];
        $billOneDetail['street'] = $status['street'];
        unset($billOneDetail['goods_album']);
        }else{
            $billOneDetail = null;
        }
        
        $this->R(['billOneDetail'=>$billOneDetail]);
    }
    /**
         * 发货
         */
        public function billPost(){
        
            $this->V(['bill_id'=>['egNum',null,true]]);
            $id = intval($_POST['bill_id']);
             
            $bill = $this->table('groupbuy_bill')->where(['id'=>$id,'is_on'=>1,'status'=>2])->get(['id'],true);
        
            if(!$bill){
                $this->R('',70009);
            }
        
            $bill = $this->table('groupbuy_bill')->where(['id'=>$id])->update(['status'=>3]);
            if(!$bill){
                $this->R('',40001);
            }
            
            $this->R();
        }
    /**
     * 查询中奖订单列表（通过订单号）
     */
    public function billListSn(){
        $this->V(['bill_sn'=>['num']]);
        $bill_sn = $_POST['bill_sn'];
        $pageInfo = $this->P();
        //调用Helper类
        $dataClass=$this->H('Bill');
        if (isset($_POST['goods_id'])) {
            $this->V(['goods_id'=>['egNum']]);
            $goods_id = intval($_POST['goods_id']);
            $good = $this->table('groupbuy_goods')->where(['is_on'=>1,'id'=>$goods_id])->get(['id'],true);
            if (!$good) {
                $this->R('',70009);
            } 
            $where = 'A.is_on = 1 and A.goods_id='.$goods_id." and A.bill_sn like '%".$bill_sn."%'";
        }elseif(isset($_POST['group_id'])){
            $this->V(['group_id'=>['egNum']]);
            $group_id = intval($_POST['group_id']);
            $group = $this->table('groupbuy_groups')->where(['is_on'=>1,'id'=>$group_id])->get(['id'],true);
            if (!$group) {
                $this->R('',70009);
            } 
            $where = 'A.is_on = 1 and A.group_id='.$group_id." and A.bill_sn like '%".$bill_sn."%'";
        }else{
            $where = "A.is_on = 1 and A.bill_sn like '%".$bill_sn."%'";
        }
        
        $order='A.id desc';
        $billList=$dataClass->getbillList(null,null,null,false,$order);
        $billList=$this->getOnePageData($pageInfo, $dataClass, 'getBillList','getBillListListLength',[$where,null,null,false,$order],true);
        if($billList){
                foreach ($billList as $k=>$v){
                    $billList[$k]['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
                    $billList[$k]['pay_time'] = date('Y-m-d H:i:s',$v['pay_time']);
                    $billList[$k]['post_time'] = date('Y-m-d H:i:s',$v['post_time']);
                    $billList[$k]['done_time'] = date('Y-m-d H:i:s',$v['done_time']);
                    unset($billList[$k]['goods_album']);
                }
        }else{
            $billList=null;
        }
        $this->R(['billList'=>$billList,'page'=>$pageInfo]);
    }
    /**
     * 查询中奖订单列表（通过订单状态）
     */
    public function billListStatus(){
        $this->V(['status'=>['num']]);
        $billstatus = $_POST['status'];
        $pageInfo = $this->P();
        //调用Helper类
        $dataClass=$this->H('Bill');
         if (isset($_POST['goods_id'])) {
            $this->V(['goods_id'=>['egNum']]);
            $goods_id = intval($_POST['goods_id']);
            $good = $this->table('groupbuy_goods')->where(['is_on'=>1,'id'=>$goods_id])->get(['id'],true);
            if (!$good) {
                $this->R('',70009);
            } 
            $where = 'A.is_on = 1 and A.goods_id='.$goods_id." and A.status like '%".$billstatus."%'";
        }elseif(isset($_POST['group_id'])){
            $this->V(['group_id'=>['egNum']]);
            $group_id = intval($_POST['group_id']);
            $group = $this->table('groupbuy_groups')->where(['is_on'=>1,'id'=>$group_id])->get(['id'],true);
            if (!$group) {
                $this->R('',70009);
            } 
            $where = 'A.is_on = 1 and A.group_id='.$group_id." and A.status like '%".$billstatus."%'";
        }else{
            $where = "A.is_on = 1 and A.status like '%".$billstatus."%'";
        }
        
        $order='A.id desc';
        $billList=$dataClass->getbillList(null,null,null,false,$order);
        $billList=$this->getOnePageData($pageInfo, $dataClass, 'getBillList','getBillListListLength',[$where,null,null,false,$order],true);
        if($billList){
                foreach ($billList as $k=>$v){
                    $billList[$k]['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
                    $billList[$k]['pay_time'] = date('Y-m-d H:i:s',$v['pay_time']);
                    $billList[$k]['post_time'] = date('Y-m-d H:i:s',$v['post_time']);
                    $billList[$k]['done_time'] = date('Y-m-d H:i:s',$v['done_time']);
                    unset($billList[$k]['goods_album']);
                }
        }else{
            $billList=null;
        }
        $this->R(['billList'=>$billList,'page'=>$pageInfo]);
    }
    /**
     * 查询中奖订单列表（通过昵称）
     */
    public function billListNickname(){
        $this->V(['nickname'=>[]]);
        $nickname = $_POST['nickname'];
        $pageInfo = $this->P();
        //调用Helper类
        $dataClass=$this->H('Bill');
         if (isset($_POST['goods_id'])) {
            $this->V(['goods_id'=>['egNum']]);
            $goods_id = intval($_POST['goods_id']);
            $good = $this->table('groupbuy_goods')->where(['is_on'=>1,'id'=>$goods_id])->get(['id'],true);
            if (!$good) {
                $this->R('',70009);
            } 
            $where = 'A.is_on = 1 and A.goods_id='.$goods_id." and C.nickname like '%".$nickname."%'";
        }elseif(isset($_POST['group_id'])){
            $this->V(['group_id'=>['egNum']]);
            $group_id = intval($_POST['group_id']);
            $group = $this->table('groupbuy_groups')->where(['is_on'=>1,'id'=>$group_id])->get(['id'],true);
            if (!$group) {
                $this->R('',70009);
            } 
            $where = 'A.is_on = 1 and A.group_id='.$group_id." and C.nickname like '%".$nickname."%'";
        }else{
            $where = "A.is_on = 1 and C.nickname like '%".$nickname."%'";
        }
        
        $order='A.id desc';
        $billList=$dataClass->getbillList(null,null,null,false,$order);
        $billList=$this->getOnePageData($pageInfo, $dataClass, 'getBillList','getBillListListLength',[$where,null,null,false,$order],true);
        if($billList){
                foreach ($billList as $k=>$v){
                    $billList[$k]['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
                    $billList[$k]['pay_time'] = date('Y-m-d H:i:s',$v['pay_time']);
                    $billList[$k]['post_time'] = date('Y-m-d H:i:s',$v['post_time']);
                    $billList[$k]['done_time'] = date('Y-m-d H:i:s',$v['done_time']);
                    unset($billList[$k]['goods_album']);
                }
        }else{
            $billList=null;
        }
        $this->R(['billList'=>$billList,'page'=>$pageInfo]);
    }
    /**
     * 查询中奖订单列表（通过商品名称）
     */
    public function billListGoodsName(){
        $this->V(['goods_name'=>[]]);
        $goods_name = $_POST['goods_name'];
        $pageInfo = $this->P();
        //调用Helper类
        $dataClass=$this->H('Bill');
         if (isset($_POST['goods_id'])) {
            $this->V(['goods_id'=>['egNum']]);
            $goods_id = intval($_POST['goods_id']);
            $good = $this->table('groupbuy_goods')->where(['is_on'=>1,'id'=>$goods_id])->get(['id'],true);
            if (!$good) {
                $this->R('',70009);
            } 
            $where = 'A.is_on = 1 and A.goods_id='.$goods_id." and B.goods_name like '%".$goods_name."%'";
        }elseif(isset($_POST['group_id'])){
            $this->V(['group_id'=>['egNum']]);
            $group_id = intval($_POST['group_id']);
            $group = $this->table('groupbuy_groups')->where(['is_on'=>1,'id'=>$group_id])->get(['id'],true);
            if (!$group) {
                $this->R('',70009);
            } 
            $where = 'A.is_on = 1 and A.group_id='.$group_id." and B.goods_name like '%".$goods_name."%'";
        }else{
            $where = "A.is_on = 1 and B.goods_name like '%".$goods_name."%'";
        }
        
        $order='A.id desc';
        $billList=$dataClass->getbillList(null,null,null,false,$order);
        $billList=$this->getOnePageData($pageInfo, $dataClass, 'getBillList','getBillListListLength',[$where,null,null,false,$order],true);
        if($billList){
                foreach ($billList as $k=>$v){
                    $billList[$k]['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
                    $billList[$k]['pay_time'] = date('Y-m-d H:i:s',$v['pay_time']);
                    $billList[$k]['post_time'] = date('Y-m-d H:i:s',$v['post_time']);
                    $billList[$k]['done_time'] = date('Y-m-d H:i:s',$v['done_time']);
                    unset($billList[$k]['goods_album']);
                }
        }else{
            $billList=null;
        }
        $this->R(['billList'=>$billList,'page'=>$pageInfo]);
    }
    /**
     * 查询中奖订单列表（通过价格）
     */
    public function billListPrice(){
        $this->V(['price'=>[]]);
        $price = $_POST['price'];
        $pageInfo = $this->P();
        //调用Helper类
        $dataClass=$this->H('Bill');
         if (isset($_POST['goods_id'])) {
            $this->V(['goods_id'=>['egNum']]);
            $goods_id = intval($_POST['goods_id']);
            $good = $this->table('groupbuy_goods')->where(['is_on'=>1,'id'=>$goods_id])->get(['id'],true);
            if (!$good) {
                $this->R('',70009);
            } 
            $where = 'A.is_on = 1 and A.goods_id='.$goods_id." and B.price like '%".$price."%'";
        }elseif(isset($_POST['group_id'])){
            $this->V(['group_id'=>['egNum']]);
            $group_id = intval($_POST['group_id']);
            $group = $this->table('groupbuy_groups')->where(['is_on'=>1,'id'=>$group_id])->get(['id'],true);
            if (!$group) {
                $this->R('',70009);
            } 
            $where = 'A.is_on = 1 and A.group_id='.$group_id." and B.price like '%".$price."%'";
        }else{
            $where = "A.is_on = 1 and B.price like '%".$price."%'";
        }
        
        $order='A.id desc';
        $billList=$dataClass->getbillList(null,null,null,false,$order);
        $billList=$this->getOnePageData($pageInfo, $dataClass, 'getBillList','getBillListListLength',[$where,null,null,false,$order],true);
        if($billList){
                foreach ($billList as $k=>$v){
                    $billList[$k]['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
                    $billList[$k]['pay_time'] = date('Y-m-d H:i:s',$v['pay_time']);
                    $billList[$k]['post_time'] = date('Y-m-d H:i:s',$v['post_time']);
                    $billList[$k]['done_time'] = date('Y-m-d H:i:s',$v['done_time']);
                    unset($billList[$k]['goods_album']);
                }
        }else{
            $billList=null;
        }
        $this->R(['billList'=>$billList,'page'=>$pageInfo]);
    }
}