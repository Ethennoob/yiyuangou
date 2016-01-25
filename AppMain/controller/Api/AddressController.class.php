<?php
    namespace AppMain\controller\Api;
    use \System\BaseClass;
    /**
     * 用户收货地址类
     */
    class AddressController extends BaseClass {
        public function testtest(){
            session_destroy();exit();
            $goods_id = 173;
            $num = 1;
            $user_id=140;
            $thematic_id=666;
            $record_id = 666;
            $codenum = $this->table('code')->where(['is_on'=>1,'is_use'=>0,'goods_id'=>$goods_id,'is_get'=>$user_id])->get(['code'],false);
            $count = count($codenum);

            for ($i=0; $i < $count; $i++) { 
                $data['code'] = $codenum[$i]['code'];
                $data['user_id'] = $user_id;
                $data['goods_id'] = $goods_id;
                $data['thematic_id'] = $thematic_id;
                $data['record_id'] = $record_id;
                $data['add_time'] = time();
                $data['ms_time'] = sprintf("%03d",floor(microtime()*1000));
                $purchase = $this->table('purchase')->save($data);
                if(!$purchase){
                    $this->R('',40001);
                }
                $codeupdate = $this->table('code')->where(['is_on'=>1,'is_use'=>0,'goods_id'=>$goods_id,'code'=>$codenum[$i]['code']])->update(['is_use'=>1,'is_get'=>0,'user_id'=>$user_id,'update_time'=>time()]);
                if(!$codeupdate){
                    $this->R('',40001);
                }
            }
            $this->R();  
        }
        public function test(){
            $post_data = array();
            $post_data["schema"] = 'json' ;
            //callbackurl请参考callback.php实现，key经常会变，请与快递100联系获取最新key
            $post_data["param"] = '{"company":"yuantong", "number":"700074134802",
            "from":"", "to":"", "key":"qMWBOgEq7733",
            "parameters":{"callbackurl":"http://onebuy.ping-qu.com/Api/Address/callbackurl"}}';
            $url='http://www.kuaidi100.com/poll';
            $o="";
            foreach ($post_data as $k => $v){
                $o.= "$k=".urlencode($v)."&";       //默认UTF-8编码格
            }
            $post_data=substr($o,0,-1);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_URL,$url);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
            //curl_exec($ch);//返回提交结果，格式与指定的格式一致（result=true代表成功）
            $p = curl_exec($ch);
        }
        public function callbackurl(){
            if (isset($_POST['param'])) {
                $Json = $_POST['param'];
                $expressdetail=json_decode($Json, true);
                $id = $expressdetail['lastResult']['nu'];
                $data = array(
                'logistics_number' => $id,
                'data' =>$Json,
                'update_time' => time(),
                );
            $record = $this->table('logistics_data')->where(['logistics_number'=>$id])->update($data);
            if (!$record) {
               $this->R('',40001);
            }
            }
        }
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
                'is_default'=>isset($_POST['is_default']) ? $_POST['is_default'] : 0,
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
                    'is_default'=>['in',[0,1],true],
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
            if ($data['is_default']==1) {

            $address = $this->table('user_address')->where(['is_on'=>1,'user_id'=>$userId])->update(['is_default'=>0]);
            if(!$address){
                $this->R('',70009);
            }
            /*$address = $this->table('user_address')->where(['is_on'=>1,'id'=>$addressId])->update(['is_default'=>1]);
            if(!$address){
                $this->R('',70009);
            }*/

            }
            //判断用户是否只有一条地址记录 如果是，设置为默认地址
           /* $address = $this->table('user_address')->where(['user_id'=>$userId,'is_on'=>1])->get(['id']);
            if(!$address){
                $data['is_default'] = 1;
            }
            if(count($address)>1 &&isset($_POST['is_default'])&&$_POST['is_default']==1){
                $data['is_default'] = 1;
                    $address = $this->table('user_address')->where(['user_id'=>$userId,'is_on'=>1])->update(['is_default'=>0]);
                    if (!$address) {
                        $this->R('',40001);
                    }
            }*/
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
        /**
         * 设置默认收货地址
         */
        public function defaultAddress(){
            $this->V(['address_id'=>['egNum',null,true]]);
            $addressId = intval($_POST['address_id']);
            $address = $this->table('user_address')->where(['id'=>$addressId,'is_on'=>1])->get(['user_id'],true);
            if(!$address){
                $this->R('',70009);
            }
            $address = $this->table('user_address')->where(['is_on'=>1,'user_id'=>$address['user_id']])->update(['is_default'=>0]);
            if(!$address){
                $this->R('',70009);
            }
            $address = $this->table('user_address')->where(['is_on'=>1,'id'=>$addressId])->update(['is_default'=>1]);
            if(!$address){
                $this->R('',70009);
            }
            $this->R();
        }
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
            $address = $this->table('bill')->where(['id'=>$billId,'status'=>1,'is_on'=>1])->get(['id'],true);
            if(!$address){
                $this->R('',70009);
            }
            $chooseaddress = $this->table('bill')->where(['is_on'=>1,'status'=>1,'id'=>$billId])->update(['address_id'=>$addressId]);
            if(!$chooseaddress){
                $this->R('',40001);
            }

            $this->R();
        }
    }
?>