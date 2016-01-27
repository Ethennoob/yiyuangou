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
        $where = 'A.is_on = 1';
        $order='A.id desc';
        $billList=$dataClass->getbillList(null,null,null,false,$order);
        $billList=$this->getOnePageData($pageInfo, $dataClass, 'getBillList','getBillListListLength',[$where,null,null,false,$order],true);
        if($billList){
                foreach ($billList as $k=>$v){
                    $status = $this->table('user')->where(['is_on'=>1,'id'=>$v['user_id']])->get(['nickname'],true);
                    $billList[$k]['nickname'] = $status['nickname'];
                    $status = $this->table('user_address')->where(['is_on'=>1,'is_default'=>1,'id'=>$v['address_id']])->get(['name','mobile','province','city','area','street'],true);
                    $billList[$k]['name'] = $status['name'];
                    $billList[$k]['mobile'] = $status['mobile'];
                    $billList[$k]['province'] = $status['province'];
                    $billList[$k]['city'] = $status['city'];
                    $billList[$k]['area'] = $status['area'];
                    $billList[$k]['street'] = $status['street'];
                }
        }else{
            $billList=false;
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
        $status = $this->table('user')->where(['is_on'=>1,'id'=>$billOneDetail['user_id']])->get(['nickname'],true);
        $billOneDetail['nickname'] = $status['nickname'];
        $status = $this->table('user_address')->where(['is_on'=>1,'is_default'=>1,'id'=>$billOneDetail['address_id']])->get(['name','mobile','province','city','area','street'],true);
        $billOneDetail['name'] = $status['name'];
        $billOneDetail['mobile'] = $status['mobile'];
        $billOneDetail['province'] = $status['province'];
        $billOneDetail['city'] = $status['city'];
        $billOneDetail['area'] = $status['area'];
        $billOneDetail['street'] = $status['street'];

        $this->R(['billOneDetail'=>$billOneDetail]);
    }
}