<?php
/**
 * 反向团购系统---支付Helper类
 * @authors 凌翔 (553299576@qq.com)
 * @date    2016-01-09 20:08:18
 * @version $Id$
 */
namespace AppMain\helper;
use System\BaseHelper;
    /*
    * 开团列表联表查询class
    */
    class PayHelper extends BaseHelper{
    
    /**
     * 订单支付
     * (虚拟模拟)微信支付回调后执行的逻辑
     * 1.查询是否符合条件的订单
     * 2.判断是否是开团订单,是开团,生成新团(group)记录(插入团长),新团成员(group_member)数据
     * 2-1 是参团，生成新团成员(group_member)数据
     * 2-2 向团表(group)插入沙发或板凳数据
     */
    public function pay($id){
    	/*$this->V(['bill_id'=>['egNum',null,true]]);
        $id = intval($_POST['bill_id']);*/
        //1
        //$id = $billid;
        $bill = $this->table('groupbuy_bill')->where(['is_on'=>1,'status'=>0,'id'=>$id])->get(['id','bill_sn','goods_id','user_id','group_id','type'],true);
        if(!$bill){
            $this->R('',70009);
        }
        //2
        if ($bill['type']==1) {
        	$data = array(
                'goods_id' => $bill['goods_id'],
                'leader' => $bill['user_id'],
                'add_time' => time()
                );
        	$group = $this->table('groupbuy_groups')->save($data);
            if(!$group){
                $this->R('',40001);
            }
        	//拿取刚刚新建的团数据
        	$groupId = $this->table('groupbuy_groups')->where(['is_on'=>1,'leader'=>$bill['user_id']])->get(['id'],true);
            if(!$groupId){
                $this->R('',70009);
            }
            $data = array(
                'group_id' => $groupId['id'],
                'user_id' => $bill['user_id'],
                'add_time' => time()
                );
        	$group_member = $this->table('groupbuy_group_member')->save($data);
        	if(!$group_member){
                $this->R('',40001);
            }
            $bill = $this->table('groupbuy_bill')->where(['is_on'=>1,'id'=>$id])->update(['group_id'=>$groupId['id']]);
        if(!$bill){
            $this->R('',70009);
        }
            //生成record
            $recorddata = array(
                'bill_id' => $id,
                'group_id' => $groupId['id'],
                'goods_id' => $bill['goods_id'],
                'user_id' => $bill['user_id'],
                'add_time' => time()
                );
            $record = $this->table('groupbuy_record')->save($recorddata);
            if(!$record){
                $this->R('',40001);
            }
        //2-1
        }elseif($bill['type']==2){
        	$data = array(
                'group_id' => $bill['group_id'],
                'user_id' => $bill['user_id'],
                'add_time' => time()
                );
        	$group_member = $this->table('groupbuy_group_member')->save($data);
        	if(!$group_member){
                $this->R('',40001);
            }
            //2-2
            $member = $this->table('groupbuy_group_member')->where(['is_on'=>1,'group_id'=>$bill['group_id']])->get(['id'],false);
            if (count($member)==2) {
            	$group = $this->table('groupbuy_groups')->where(['is_on'=>1,'id'=>$bill['group_id']])->update(['soft'=>$bill['user_id'],'update_time'=>time()]);
            	if(!$group){
                $this->R('',40001);
                }
            }elseif (count($member)==3) {
            	$group = $this->table('groupbuy_groups')->where(['is_on'=>1,'id'=>$bill['group_id']])->update(['stool'=>$bill['user_id'],'update_time'=>time()]);
            	if(!$group){
                $this->R('',40001);
                }
            }
            //生成record
            $data = array(
                'bill_id' => $id,
                'group_id' => $bill['group_id'],
                'goods_id' => $bill['goods_id'],
                'user_id' => $bill['user_id'],
                'add_time' => time()
                );
            $record = $this->table('groupbuy_record')->save($data);
            if(!$record){
                $this->R('',40001);
            }
        }
        $bill = $this->table('groupbuy_bill')->where(['is_on'=>1,'id'=>$id])->update(['status'=>1,'pay_time'=>time(),'update_time'=>time()]);
        if(!$bill){
            $this->R('',70009);
        }
        return true;
    }
}