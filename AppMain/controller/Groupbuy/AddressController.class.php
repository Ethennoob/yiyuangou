<?php
    namespace AppMain\controller\Groupbuy;
    use \System\BaseClass;
    /**
     * 用户收货地址类
     */
    class AddressController extends BaseClass {
        /**
         * 设置订单收货地址
         */
        public function chooseAddress(){
            $this->V([
                'address_id'=>['egNum',null,true],
                'bill_id'   =>['egNum',null,true],
                ]);
            $addressId = intval($_POST['address_id']);
            $billId = intval($_POST['bill_id']);
            $address = $this->table('user_address')->where(['id'=>$addressId,'is_on'=>1])->get(['id'],true);
            if(!$address){
                $this->R('',70009);
            }
            $address = $this->table('groupbuy_bill')->where(['id'=>$billId,'is_on'=>1])->get(['id'],true);
            if(!$address){
                $this->R('',70009);
            }
            $chooseaddress = $this->table('groupbuy_bill')->where(['is_on'=>1,'id'=>$billId])->update(['address_id'=>$addressId]);
            if(!$chooseaddress){
                $this->R('',40001);
            }

            $this->R();
        }
    }
?>