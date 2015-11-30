<?php
	namespace AppMain\controller\Admin;
	use \System\BaseClass;
	/**
     * 用户收货地址类
     */
	class AddressController extends BaseClass {
		/**
	     * 添加用户收货地址
	     */
		public function addressAdd(){
	        $this->V(['id'=>['egNum',null,true]]);
	        //获取用户id
	        $userId = intval($_POST['id']);
	        $rule = [
                    'province' =>[],
                    'city'     =>[],
                    'area'     =>[],
                    'mobile'   =>['mobile'],
                    'name'     =>[],
                ];
            $this->V($rule);
            foreach ($rule as $k => $v) {
            	$data[$k] = $_POST[$k];
            }
            $data = array(
                'user_id'  =>$userId;
        		'province' =>$data['province'],
                'city'     =>$data['city'],
                'area'     =>$data['area'],
                'mobile'   =>$data['mobile'],
                'name'     =>$data['name'],
                'postcode' =>isset($_POST['postcode']) ? $_POST['postcode'] : '',
                'is_default'=>isset($_POST['postcode']) ? $_POST['postcode'] : '',
                'add_time'          =>time(),
            	);
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
			$this->V(['id'=>['egNum',null,true]]);
	        $addressId = intval($_POST['id']);
	        $rule = [
	        		'id'       =>[],
                	'province' =>[],
                    'city'     =>[],
                    'area'     =>[],
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
            $where=['is_on'=>1];
            $pageInfo = $this->P();
            $class = $this->table('user_address')->where($where)->order('add_time desc');
            //查询并分页
            $addresslist = $this->getOnePageData($pageInfo,$class,'get','getListLength',null,false);
            if($addresslist){
                foreach ($addresslist as $k=>$v){
                    $addresslist[$k]['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
                    $addresslist[$k]['update_time'] = date('Y-m-d H:i:s',$v['update_time']);
                }
            }else{
                $addresslist = null;
            }
            $this->R(['addresslist'=>$addresslist,'pageInfo'=>$pageInfo]);
        }

        /**
         * 查询一条用户收货地址信息
         */
        public function addressOneList(){
            $this->V(['id'=>['egNum',null,true]]);
	        $addressId = intval($_POST['id']);
            if (!$addressDetail){
                //查询一条数据
                $address = $this->table('user_address')->where(['is_on'=>1,'id'=>$addressId])->get(null,true);
                if(!$address){
                    $this->R('',70009);
                }
                $address['update_time'] = date('Y-m-d H:i:s',$address['update_time']);
                $address['add_time'] = date('Y-m-d H:i:s',$address['add_time']);
            }    
            $this->R(['address'=>$address]);
        }

        /**
         *删除一条用户收货地址数据（设置数据库字段为0，相当于回收站）
         */
        public function addressDelete(){
        
            $this->V(['id'=>['egNum',null,true]]);
            $id = intval($_POST['id']);
             
            $address = $this->table('user_address')->where(['id'=>$id,'is_on'=>1])->get(['id'],true);
        
            if(!$address){
                $this->R('',70009);
            }
        
            $address = $this->table('user_address')->where(['id'=>$id])->update(['is_on'=>0]);
            if(!$address){
                $this->R('',40001);
            }
            $this->R();
        }
        
        /**
         *删除一条用户收货地址数据（清除数据）
         */
        public function addressDeleteconfirm(){
            $this->V(['id'=>['egNum',null,true]]);
            $id = intval($_POST['id']);
             
            $address = $this->table('user_address')->where(['id'=>$id,'is_on'=>1])->get(['id'],true);
            if(!$address){
                $this->R('',70009);
            }
            $address = $this->table('user_address')->where(['id'=>$id])->delete();
            if(!$address){
                $this->R('',40001);
            }
            $this->R();
        }
	}
?>