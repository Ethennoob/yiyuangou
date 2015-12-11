<?php
    namespace AppMain\controller\Api;
    use \System\BaseClass;
    /**
     * 用户收货地址类
     */
    class AddressController extends BaseClass {
        /**
         * 添加用户收货地址
         */
        public function addressAdd(){
            $this->V(['user_id'=>['egNum',null,true]]);
            //获取用户id
            $userId = intval($_POST['user_id']);
            $rule = [
                    'province' =>[],
                    'city'     =>[],
                    'area'     =>[],
                    'street'   =>[],
                    'mobile'   =>['mobile'],
                    'name'     =>[],
                ];
            $this->V($rule);
            foreach ($rule as $k => $v) {
                $data[$k] = $_POST[$k];
            }
            $data = array(
                'user_id'   =>$userId,
                'province'  =>$data['province'],
                'city'      =>$data['city'],
                'area'      =>$data['area'],
                'street'    =>$data['street'],
                'mobile'    =>$data['mobile'],
                'name'      =>$data['name'],
                'postcode'  =>isset($_POST['postcode']) ? $_POST['postcode'] : '',
                'is_default'=>isset($_POST['is_default']) ? $_POST['is_default'] : '',
                'add_time'  =>time(),
                );

            //判断用户是否只有一条地址记录 如果是，设置为默认地址
            $address = $this->table('user_address')->where(['user_id'=>$userId,'is_on'=>1])->get(['id'],true);
            if(!$address){
                $data['is_default'] = 1;
            }

            $address = $this->table('user_address')->save($data);
            if (!$address) {
                $this->R('',40001);
            }
            $this->R();
        }

        /**
         * 修改用户收货地址
         */
        public function addressEdit(){
            $this->V(['user_id'=>['egNum',null,true]]);
            //获取用户id
            $userId = intval($_POST['user_id']);
            $this->V(['address_id'=>['egNum',null,true]]);
            $addressId = intval($_POST['address_id']);
            $rule = [
                    'province' =>[],
                    'city'     =>[],
                    'area'     =>[],
                    'street'   =>[],
                    'mobile'   =>['mobile'],
                    'name'     =>[],
            ];
            $this->V($rule);

            $address = $this->table('user_address')->where(['id'=>$addressId,'is_on'=>1])->get(['id'],true);
            if(!$address){
                $this->R('',70009);
            }

            unset($rule['id']);
            foreach ($rule as $k=>$v){
                if(isset($_POST[$k])){
                    $data[$k] = $_POST[$k];
                }
            }

            //判断用户是否只有一条地址记录 如果是，设置为默认地址
            $address = $this->table('user_address')->where(['user_id'=>$userId,'is_on'=>1])->get(['id']);
            if(!$address){
                $data['is_default'] = 1;
            }
            if(count($address)>1 &&isset($_POST['is_default'])&&$_POST['is_default']==1){
                $data['is_default'] = 1;
                    $address = $this->table('user_address')->where(['user_id'=>$userId,'is_on'=>1])->update(['is_default'=>0]);
                    if (!$address) {
                        $this->R('',40001);
                    }
            }
            $data['update_time'] = time();
            $address = $this->table('user_address')->where(['id'=>$addressId])->update($data);
            if(!$address){
                $this->R('',40001);
            }
            $this->R();
        }

        /**
         * 用户收货地址列表
         */
        public function addressList(){
            $this->V(['user_id'=>['egNum',null,true]]);
            //获取用户id
            $userId = intval($_POST['user_id']);
            $where=['is_on'=>1,'user_id'=>$userId];
            $pageInfo = $this->P();
            $class = $this->table('user_address')->where($where)->order('add_time desc');
            //查询并分页
            $addresslist = $this->getOnePageData($pageInfo,$class,'get','getListLength',null,false);
            if($addresslist){
                foreach ($addresslist as $k=>$v){
                    $addresslist[$k]['add_time'] = $v['add_time'];
                    $addresslist[$k]['update_time'] = $v['update_time'];
                }
            }else{
                $addresslist = null;
            }
            $this->R(['addresslist'=>$addresslist,'pageInfo'=>$pageInfo]);
        }

        /**
         * 查询一条用户收货地址信息
         */
        public function addressOneDetail(){
            $this->V(['address_id'=>['egNum',null,true]]);
            $addressId = intval($_POST['address_id']);
            $address = $this->table('user_address')->where(['is_on'=>1,'id'=>$addressId])->get(null,true);
            if(!$address){
                $this->R('',70009);
            }
            $address['update_time'] = $address['update_time'];
            $address['add_time'] = $address['add_time'];
            $this->R(['address'=>$address]);
        }
        
        /**
         *删除一条用户收货地址数据（清除数据）
         */
        public function addressDeleteconfirm(){

             $idArray = $_POST['address_id'];
             //var_dump($idArray);exit();
           //$idArray = array(0=>2,1=>4,2=>5);
             foreach ($idArray as $v) {
               $data['id']=$v;  
             $this->V(['id'=>['egNum',null,false]],$data);
            $address = $this->table('user_address')->where(['id'=>$v,'is_on'=>1])->get(['id'],true);
            if(!$address){
                $this->R('',70009);
            }
            $address = $this->table('user_address')->where(['id'=>$v])->delete();
            if(!$address){
                $this->R('',40001);
            }
            }
            $this->R();
        }
    }
?>