<?php
/**
 * 反向团购系统---订单类
 * @authors 凌翔 (553299576@qq.com)
 * @date    2016-01-09 13:18:21
 * @version $Id$
 */

namespace AppMain\controller\Groupbuy;
use \System\BaseClass;

class BillController extends Baseclass {
    /**
     * 开团
     * 购买商品
     * 生成开团订单
     */
    public function openGroupBill(){

        $rule = [
                    'goods_id'    =>['egNum'],
                    'user_id'     =>['egNum'],
                ];
                $this->V($rule); 

            $goods_id    = $_POST['goods_id'];
            $user_id     = $_POST['user_id'];
            //是否冻结用户
                $user = $this->table('user')->where(['is_on'=>1,'id'=>$user_id,'is_froze'=>0])->get(['id'],true);
                if(!$user){
                    $this->R('',90008);
                }

            //判断是否有这个商品
            $good = $this->table('groupbuy_goods')->where(['is_on'=>1,'id'=>$goods_id])->get(['id'],true);
            if(!$good){
                $this->R('',70009);
            }
            //判断是否有这个用户
            $user = $this->table('user')->where(['is_on'=>1,'id'=>$user_id])->get(['id'],true);
            if(!$user){
                $this->R('',70009);
            }

            //查询此用户在一元购的默认收货地址
            $address = $this->table('user_address')->where(['is_on'=>1,'is_default'=>1,'user_id'=>$user_id])->get(['id'],true);
            if(!$address){
                $address['id'] = null;
            }


            //生成购买记录
            $data = array(
                'bill_sn' => date("YmdHis").rand(10000,99999),
                'goods_id' => $goods_id,
                'user_id' => $user_id,
                'address_id' =>$address['id'],
                'type' => 1,
                'add_time' => time()
                );
            $bill = $this->table('groupbuy_bill')->save($data);
                if(!$bill){
                    $this->R('',40001);
                }
            $billid = $this->table('groupbuy_bill')->where(['is_on'=>1,'user_id'=>$user_id,'goods_id'=>$goods_id])->order('id desc')->get(['id'],true);
            if(!$billid){
                $this->R('',70009);
            }

            $this->R(['billid'=>$billid['id']]); 
    }   
    /**
     * 参团
     * 购买商品
     * 生成参团订单
     */
    public function joinGroupBill(){

        $rule = [
                    'goods_id'    =>['egNum'],
                    'user_id'     =>['egNum'],
                    'group_id'     =>['egNum'],
                ];
                $this->V($rule); 

            $goods_id    = $_POST['goods_id'];
            $user_id     = $_POST['user_id'];
            $group_id     = $_POST['group_id'];
            //是否冻结用户
                $user = $this->table('user')->where(['is_on'=>1,'id'=>$user_id,'is_froze'=>0])->get(['id'],true);
                if(!$user){
                    $this->R('',90008);
                }

            //判断是否有这个商品
            $good = $this->table('groupbuy_goods')->where(['is_on'=>1,'id'=>$goods_id])->get(['id'],true);
            if(!$good){
                $this->R('',70009);
            }
            //判断是否有这个用户
            $user = $this->table('user')->where(['is_on'=>1,'id'=>$user_id])->get(['id'],true);
            if(!$user){
                $this->R('',70009);
            }
            //判断是否有这个团
            $group = $this->table('groupbuy_groups')->where(['is_on'=>1,'id'=>$group_id])->get(['id'],true);
            if(!$group){
                $this->R('',70009);
            }
            //判断是否已经参与了此团
            $group_member = $this->table('groupbuy_group_member')->where(['is_on'=>1,'group_id'=>$group_id,'user_id'=>$user_id])->get(['id'],true);
            if($group_member){
                $this->R('',30001);
            }
            //查询此用户在一元购的默认收货地址
            $address = $this->table('user_address')->where(['is_on'=>1,'is_default'=>1,'user_id'=>$user_id])->get(['id'],true);
            if(!$address){
                $address['id'] = null;
            }


            //生成购买记录
            $data = array(
                'bill_sn' => date("YmdHis").rand(10000,99999),
                'group_id' => $group_id,
                'goods_id' => $goods_id,
                'user_id' => $user_id,
                'address_id' =>$address['id'],
                'type' => 2,
                'add_time' => time()
                );
            $bill = $this->table('groupbuy_bill')->save($data);
                if(!$bill){
                    $this->R('',40001);
                }
            $billid = $this->table('groupbuy_bill')->where(['is_on'=>1,'user_id'=>$user_id,'goods_id'=>$goods_id,'group_id'=>$group_id])->order('id desc')->get(['id'],true);
            if(!$billid){
                $this->R('',70009);
            }

            $this->R(['billid'=>$billid['id']]); 
            $this->R(); 
    }
    /**
     * 用户的购买记录,订单列表(分页)     
     * user_id
     */
    public function billList(){

        $this->V(['user_id'=>['egNum']]);
        $id = intval($_POST['user_id']);
        if (isset($_POST['status'])) {
            $this->V(['status'=>['in',[0,3]]]);
            $status = intval($_POST['status']);
            $where = 'A.is_on = 1 and A.user_id='.$id.' and A.status ='.$status;
        }else{
            $where = 'A.is_on = 1 and A.user_id='.$id;
        }
        $pageInfo = $this->P();
        //调用Helper类
        $dataClass=$this->H('Bill');
        $order='A.add_time desc';
        $billList=$dataClass->getbillList(null,null,null,false,$order);
        $billList=$this->getOnePageData($pageInfo, $dataClass, 'getBillList','getBillListListLength',[$where,null,null,false,$order],true);
        if($billList){
            foreach ($billList as $k => $v) {
            $good = $this->table('groupbuy_goods')->where(['id'=>$v['goods_id']])->get(['goods_album'],true);
            $ImgUrl=explode(';', $good['goods_album']);
            $billList[$k]['goods_img'] = $ImgUrl[0];
            unset($billList[$k]['goods_album'] );
            $billList[$k]['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
            }
        }else{
            $billList=false;
        }
        $this->R(['billList'=>$billList,'page'=>$pageInfo]);
    }
    /**
     * 订单详情
     * goods_id
     * user_id
     */
    public function billOneDetail(){

        $this->V(['bill_id'=>['egNum',null,true]]);
        $id = intval($_POST['bill_id']);

        $dataClass=$this->H('Bill');
        $where = 'A.is_on = 1 and A.id='.$id;
        
        $billOneDetail=$dataClass->getbillList($where,null,null,true);
        if ($billOneDetail) {
            $good = $this->table('groupbuy_goods')->where(['id'=>$billOneDetail['goods_id']])->get(['goods_album'],true);
            $ImgUrl=explode(';', $good['goods_album']);
            $billOneDetail['goods_img'] = $ImgUrl[0];
            unset($billOneDetail['goods_album'] );
            $address = $this->table('user_address')->where(['is_on'=>1,'id'=>$billOneDetail['address_id']])->get(null,true);
            $billOneDetail['province'] = $address['province'];
            $billOneDetail['city'] = $address['city'];
            $billOneDetail['area'] = $address['area'];
            $billOneDetail['street'] = $address['street'];
            $billOneDetail['mobile'] = $address['mobile'];
            $billOneDetail['name'] = $address['name'];
            $billOneDetail['add_time'] = date('Y-m-d H:i:s',$billOneDetail['add_time']);
        }else{
            $billOneDetail=null;
        }
        
        //返回数据，参见System/BaseClass.class.php方法
        $this->R(['billOneDetail'=>$billOneDetail]);
    }
    /**
     * 取消订单
     */
    public function cancelBill(){
        $this->V(['bill_id'=>['egNum',null,true]]);
        $id = intval($_POST['bill_id']);
        //判断是否有这个订单
        $bill = $this->table('groupbuy_bill')->where(['is_on'=>1,'id'=>$id,'status'=>0])->get(['id'],true);
            if(!$bill){
                $this->R('',70009);
            }
        $cancel = $this->table('groupbuy_bill')->where(['is_on'=>1,'id'=>$id])->update(['status'=>5,'update_time'=>time()]);
        if (!$cancel) {
            $this->R('',40001);
        }
        $this->R();

    }
    /**
     * 删除订单
     */
    public function deleteBill(){
        $this->V(['bill_id'=>['egNum',null,true]]);
        $id = intval($_POST['bill_id']);
        //判断是否有这个订单
        $bill = $this->table('groupbuy_bill')->where(['is_on'=>1,'id'=>$id,'status'=>5])->get(['id'],true);
            if(!$bill){
                $this->R('',70009);
            }
        $cancel = $this->table('groupbuy_bill')->where(['is_on'=>1,'id'=>$id])->update(['is_on'=>0]);
        if (!$cancel) {
            $this->R('',40001);
        }
        $this->R();

    }
    /**
     * 收货订单
     */
    public function doneBill(){
        $this->V(['bill_id'=>['egNum',null,true]]);
        $id = intval($_POST['bill_id']);
        //判断是否有这个订单
        $bill = $this->table('groupbuy_bill')->where(['is_on'=>1,'id'=>$id,'status'=>3])->get(['id'],true);
            if(!$bill){
                $this->R('',70009);
            }
        $cancel = $this->table('groupbuy_bill')->where(['is_on'=>1,'id'=>$id])->update(['status'=>4,'done_time'=>time()]);
        if (!$cancel) {
            $this->R('',40001);
        }
        $this->R();

    }
    
    
}