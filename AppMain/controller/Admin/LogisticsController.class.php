<?php
	namespace AppMain\controller\Admin;
	use \System\BaseClass;
	/**
     * 物流类
     */
	class LogisticsController extends BaseClass {
        /**
         * 检查用户是否有填写收货地址
         */
        public function checkAddress(){
            $this->V(['user_id'=>['egNum',null,true]]);
            //获取订单id
            $id = intval($_POST['user_id']);
            $userAddr = $this->table('user_address')->where(['user_id'=>$id,'is_default'=>1])->get(['id'],true);
            if (!$userAddr) {
                $this->R('',70009);
            }
        }
		/**
	     * 添加物流单
	     */
		public function logisticsAdd(){
			$this->V(['bill_id'=>['egNum',null,true]]);
			//获取订单id
	        $billId = intval($_POST['bill_id']);

	        $this->V(['user_id'=>['egNum',null,true]]);
	        //获取用户id
	        $userId = intval($_POST['user_id']);

	        $rule = [
                    'logistics_number'  =>[],
                    'logistics_name'    =>[],
                ];
            $this->V($rule);
            foreach ($rule as $k => $v) {
            	$data[$k] = $_POST[$k];
            }
            $userAddr = $this->table('bill')->where(['id'=>$bill_id])->get(['address_id','goods_id'],true);
            if (!$userAddr) {
                $userAddr['address_id'] =null;
            }
            $company_id = $this->table('goods')->where(['id'=>$userAddr['goods_id']])->get(['company_id'],true);
            if (!$company_id) {
                $company_id['company_id'] =null;
            }
            $data = array(
            		'logistics_number'  =>$data['logistics_number'],
                    'logistics_name'    =>$data['logistics_name'],
                    'logistics_status'  =>0,
                    'company_id'        =>$company_id['company_id'],
                    'bill_id'           =>$billId,
                    'user_address_id'   =>$userAddr['address_id'],
                    'add_time'          =>time(),
            	);
            $logistics = $this->table('logistics')->save($data);
            if (!$logistics) {
            	$this->R('',40001);
            }
            //设置bill表is_post状态为1
            $billPost = $this->table('bill')->where(['id'=>$billId])->update(['is_post'=>1,'status'=>2]);
            if (!$billPost) {
                $this->R('',40001);
            }
            $this->R();
		}

		/**
	     * 修改物流单
	     */
		public function logisticsEdit(){
			$this->V(['id'=>['egNum',null,true]]);
	        $logisticsId = intval($_POST['id']);
	        $rule = [
	        		'id'                =>[],
                	'logistics_number'  =>[],
                    'logistics_name'    =>[],
                    //'logistics_status'  =>[],
            ];
            $this->V($rule);
            $logistics = $this->table('logistics')->where(['id'=>$logisticsId,'is_on'=>1])->get(['id'],true);
            if(!$logistics){
                $this->R('',70009);
            }

            unset($rule['id']);
            foreach ($rule as $k=>$v){
                if(isset($_POST[$k])){
                    $data[$k] = $_POST[$k];
                }
            }

            $logistics = $this->table('logistics')->where(['id'=>$logisticsId])->update($data);
            if(!$logistics){
                $this->R('',40001);
            }
            $this->R();
		}

		/**
         * 物流列表
         */
        public function logisticsList(){
            $this->V(['company_id'=>['egNum',null,true]]);
            $id = intval($_POST['company_id']);
            $where=['is_on'=>1,'company_id'=>$id];
            $pageInfo = $this->P();
            $class = $this->table('logistics')->where($where)->order('add_time desc');
            //查询并分页
            $logisticslist = $this->getOnePageData($pageInfo,$class,'get','getListLength',null,false);
            if($logisticslist){
                foreach ($logisticslist as $k=>$v){
                    $logisticslist[$k]['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
                    $logisticslist[$k]['update_time'] = date('Y-m-d H:i:s',$v['update_time']);
                    $id = $v['logistics_number'];
                    $express = new \System\lib\Express\Express();
                    $expressdetail = $express->getorder($id);
                    if (@$expressdetail['state'] == null) {
                        @$expressdetail['state'] = "0";
                    }else{
                    $updateExpress = $this->table('logistics')->where(['is_on'=>1,'logistics_number'=>$id])->update(['logistics_status'=>$expressdetail['state']]);
                    if (!$updateExpress) {
                        $this->R('',70009);
                    }
                    }
                }
            }else{
                $logisticslist = null;
            }
            $this->R(['logisticslist'=>$logisticslist,'pageInfo'=>$pageInfo]);
        }

        /**
         * 查询一条物流信息
         */
        public function logisticsOneDetail(){
            $this->V(['logistics_id'=>['egNum',null,true]]);
	        $logisticsId = intval($_POST['logistics_id']);
                //查询一条数据
                $logistics = $this->table('logistics')->where(['is_on'=>1,'id'=>$logisticsId])->get(null,true);
                if(!$logistics){
                    $this->R('',70009);
                }
                //查询一条数据
                
                $logistics['update_time'] = date('Y-m-d H:i:s',$logistics['update_time']);
                $logistics['add_time'] = date('Y-m-d H:i:s',$logistics['add_time']);
                $user_address = $this->table('user_address')->where(['is_on'=>1,'id'=>$logistics['user_address_id']])->get(['user_id','province','city','area','street','mobile','name'],true);
                if ($user_address) {
                    $logistics = array_merge($logistics, $user_address);
                }
                //$logistics = array_merge($logistics, $user_address);

            $this->R(['logistics'=>$logistics]);
        }
        /**
         * 物流列表查询（通过物流单号）
         */
        public function logisticsListNumber(){
            $this->V(['logistics_number'=>['num']]);
            $logistics_number = $_POST['logistics_number'];
             if (empty($_POST['company_id'])||!isset($_POST['company_id'])) {
                
            $where='is_on = 1 and logistics_number like "%'.$logistics_number.'%"';
        }else{
            $this->V(['company_id'=>['egNum',null,true]]);
            $id = $_POST['company_id'];
            $where='is_on = 1 and company_id = "'.$id.'" and logistics_number like "%'.$logistics_number.'%"';
        }
            $pageInfo = $this->P();
            $class = $this->table('logistics')->where($where)->order('add_time desc');
            //查询并分页
            $logisticslist = $this->getOnePageData($pageInfo,$class,'get','getListLength',null,false);
            if($logisticslist){
                foreach ($logisticslist as $k=>$v){
                    $logisticslist[$k]['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
                    $logisticslist[$k]['update_time'] = date('Y-m-d H:i:s',$v['update_time']);
                    $id = $v['logistics_number'];
                    $express = new \System\lib\Express\Express();
                    $expressdetail = $express->getorder($id);
                    if (@$expressdetail['state'] == null) {
                        @$expressdetail['state'] = "0";
                    }else{
                    $updateExpress = $this->table('logistics')->where(['is_on'=>1,'logistics_number'=>$id])->update(['logistics_status'=>$expressdetail['state']]);
                    if (!$updateExpress) {
                        $this->R('',70009);
                    }
                    }
                }
            }else{
                $logisticslist = null;
            }
            $this->R(['logisticslist'=>$logisticslist,'pageInfo'=>$pageInfo]);
        }
        /**
         * 物流列表查询（通过物流单号）
         */
        public function logisticsListSn(){
            $this->V(['bill_sn'=>['num']]);
            $bill_sn = $_POST['bill_sn'];
             if (empty($_POST['company_id'])||!isset($_POST['company_id'])) {
            $where='is_on = 1 and bill_sn like "%'.$bill_sn.'%"';
        }else{
            $this->V(['company_id'=>['egNum',null,true]]);
            $id = $_POST['company_id'];
            $where='is_on = 1 and company_id = "'.$id.'" and bill_sn like "%'.$bill_sn.'%"';
        }
        $value = $this->table('bill')->where($where)->limit(0,1)->order('add_time desc')->get(['id'],false);
            $pageInfo = $this->P();
            $class = $this->table('logistics')->where(['is_on'=>1,'bill_id'=>$value[0]['id']])->order('add_time desc');
            //查询并分页
            $logisticslist = $this->getOnePageData($pageInfo,$class,'get','getListLength',null,false);
            if($logisticslist){
                foreach ($logisticslist as $k=>$v){
                    $logisticslist[$k]['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
                    $logisticslist[$k]['update_time'] = date('Y-m-d H:i:s',$v['update_time']);
                    $id = $v['logistics_number'];
                    $express = new \System\lib\Express\Express();
                    $expressdetail = $express->getorder($id);
                    if (@$expressdetail['state'] == null) {
                        @$expressdetail['state'] = "0";
                    }else{
                    $updateExpress = $this->table('logistics')->where(['is_on'=>1,'logistics_number'=>$id])->update(['logistics_status'=>$expressdetail['state']]);
                    if (!$updateExpress) {
                        $this->R('',70009);
                    }
                    }
                }
            }else{
                $logisticslist = null;
            }
            $this->R(['logisticslist'=>$logisticslist,'pageInfo'=>$pageInfo]);
        }
        /**
         * 物流列表查询（通过物流公司）
         */
        public function logisticsListName(){
            $this->V(['logistics_name'=>[]]);
            $logistics_name = $_POST['logistics_name'];
             if (empty($_POST['company_id'])||!isset($_POST['company_id'])) {
                
             $where='is_on = 1 and logistics_name like "%'.$logistics_name.'%"';
        }else{
            $this->V(['company_id'=>['egNum',null,true]]);
            $id = $_POST['company_id'];
            $where='is_on = 1 and company_id = "'.$id.'" and logistics_name like "%'.$logistics_name.'%"';
        }
           
            $pageInfo = $this->P();
            $class = $this->table('logistics')->where($where)->order('add_time desc');
            //查询并分页
            $logisticslist = $this->getOnePageData($pageInfo,$class,'get','getListLength',null,false);
            if($logisticslist){
                foreach ($logisticslist as $k=>$v){
                    $logisticslist[$k]['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
                    $logisticslist[$k]['update_time'] = date('Y-m-d H:i:s',$v['update_time']);
                    $id = $v['logistics_number'];
                    $express = new \System\lib\Express\Express();
                    $expressdetail = $express->getorder($id);
                    if (@$expressdetail['state'] == null) {
                        @$expressdetail['state'] = "0";
                    }else{
                    $updateExpress = $this->table('logistics')->where(['is_on'=>1,'logistics_number'=>$id])->update(['logistics_status'=>$expressdetail['state']]);
                    if (!$updateExpress) {
                        $this->R('',70009);
                    }
                    }
                }
            }else{
                $logisticslist = null;
            }
            $this->R(['logisticslist'=>$logisticslist,'pageInfo'=>$pageInfo]);
        }
        /**
         * 物流列表查询（通过物流狀態）(0已发送，1揽件，2疑难，3签收，4退签，5派件，6退回)
         */
        public function logisticsListStatus(){
            $this->V(['logistics_status'=>[]]);
            $logistics_status = $_POST['logistics_status'];
             if (empty($_POST['company_id'])||!isset($_POST['company_id'])) {
                
             $where='is_on = 1 and logistics_status = "'.$logistics_status.'"';
        }else{
            $this->V(['company_id'=>['egNum',null,true]]);
            $id = $_POST['company_id'];
            $where='is_on = 1 and company_id = "'.$id.'" and logistics_status = "'.$logistics_status.'"';
        }
           
            $pageInfo = $this->P();
            $class = $this->table('logistics')->where($where)->order('add_time desc');
            //查询并分页
            $logisticslist = $this->getOnePageData($pageInfo,$class,'get','getListLength',null,false);
            if($logisticslist){
                foreach ($logisticslist as $k=>$v){
                    $logisticslist[$k]['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
                    $logisticslist[$k]['update_time'] = date('Y-m-d H:i:s',$v['update_time']);
                    $id = $v['logistics_number'];
                    $express = new \System\lib\Express\Express();
                    $expressdetail = $express->getorder($id);
                    if (@$expressdetail['state'] == null) {
                        @$expressdetail['state'] = "0";
                    }else{
                    $updateExpress = $this->table('logistics')->where(['is_on'=>1,'logistics_number'=>$id])->update(['logistics_status'=>$expressdetail['state']]);
                    if (!$updateExpress) {
                        $this->R('',70009);
                    }
                    }
                }
            }else{
                $logisticslist = null;
            }
            $this->R(['logisticslist'=>$logisticslist,'pageInfo'=>$pageInfo]);
        }
        /**
         * 物流列表查询（通过物流单号）
         */
        public function logisticsListNickname(){
            $this->V(['name'=>[]]);
            $name = $_POST['name'];
            $where='is_on = 1 and name like "%'.$name.'%"';
            $value = $this->table('user_address')->where($where)->limit(0,1)->order('add_time desc')->get(['id'],false);
             if (empty($_POST['company_id'])||!isset($_POST['company_id'])) {
            $where1='is_on = 1 and user_address_id = "'.$value[0]['id'].'"';
        }else{
            $this->V(['company_id'=>['egNum',null,true]]);
            $id = $_POST['company_id'];
            $where1='is_on = 1 and company_id = "'.$id.'" and user_address_id = "'.$value[0]['id'].'"';
        }
        //$value = $this->table('bill')->where($where)->limit(0,1)->order('add_time desc')->get(['id'],false);
            $pageInfo = $this->P();
            $class = $this->table('logistics')->where($where1)->order('add_time desc');
            //查询并分页
            $logisticslist = $this->getOnePageData($pageInfo,$class,'get','getListLength',null,false);
            if($logisticslist){
                foreach ($logisticslist as $k=>$v){
                    $logisticslist[$k]['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
                    $logisticslist[$k]['update_time'] = date('Y-m-d H:i:s',$v['update_time']);
                    $id = $v['logistics_number'];
                    $express = new \System\lib\Express\Express();
                    $expressdetail = $express->getorder($id);
                    if (@$expressdetail['state'] == null) {
                        @$expressdetail['state'] = "0";
                    }else{
                    $updateExpress = $this->table('logistics')->where(['is_on'=>1,'logistics_number'=>$id])->update(['logistics_status'=>$expressdetail['state']]);
                    if (!$updateExpress) {
                        $this->R('',70009);
                    }
                    }
                }
            }else{
                $logisticslist = null;
            }
            $this->R(['logisticslist'=>$logisticslist,'pageInfo'=>$pageInfo]);
        }
        /**
         * 物流列表查询（通过物流单号）
         */
        public function logisticsListMobile(){
            $this->V(['mobile'=>['mobile']]);
            $name = intval($_POST['mobile']);
            $where='is_on = 1 and mobile = "'.$name.'"';
            $value = $this->table('user_address')->where($where)->limit(0,1)->order('add_time desc')->get(['id'],false);
             if (empty($_POST['company_id'])||!isset($_POST['company_id'])) {
            $where1='is_on = 1 and user_address_id = "'.$value[0]['id'].'"';
        }else{
            $this->V(['company_id'=>['egNum',null,true]]);
            $id = $_POST['company_id'];
            $where1='is_on = 1 and company_id = "'.$id.'" and user_address_id = "'.$value[0]['id'].'"';
        }
        //$value = $this->table('bill')->where($where)->limit(0,1)->order('add_time desc')->get(['id'],false);
            $pageInfo = $this->P();
            $class = $this->table('logistics')->where($where1)->order('add_time desc');
            //查询并分页
            $logisticslist = $this->getOnePageData($pageInfo,$class,'get','getListLength',null,false);
            if($logisticslist){
                foreach ($logisticslist as $k=>$v){
                    $logisticslist[$k]['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
                    $logisticslist[$k]['update_time'] = date('Y-m-d H:i:s',$v['update_time']);
                    $id = $v['logistics_number'];
                    $express = new \System\lib\Express\Express();
                    $expressdetail = $express->getorder($id);
                    if (@$expressdetail['state'] == null) {
                        @$expressdetail['state'] = "0";
                    }else{
                    $updateExpress = $this->table('logistics')->where(['is_on'=>1,'logistics_number'=>$id])->update(['logistics_status'=>$expressdetail['state']]);
                    if (!$updateExpress) {
                        $this->R('',70009);
                    }
                    }
                }
            }else{
                $logisticslist = null;
            }
            $this->R(['logisticslist'=>$logisticslist,'pageInfo'=>$pageInfo]);
        }
        /**
         *删除一条物流数据（设置数据库字段为0，相当于回收站）
         */
        public function logisticsDelete(){
        
            $this->V(['logistics_id'=>['egNum',null,true]]);
            $id = intval($_POST['logistics_id']);
             
            $logistics = $this->table('logistics')->where(['id'=>$id,'is_on'=>1])->get(['id'],true);
        
            if(!$logistics){
                $this->R('',70009);
            }
        
            $logistics = $this->table('logistics')->where(['id'=>$id])->update(['is_on'=>0]);
            if(!$logistics){
                $this->R('',40001);
            }
            $this->R();
        }
        
        /**
         *删除一条物流数据（清除数据）
         */
        public function logisticsDeleteconfirm(){
            $this->V(['logistics_id'=>['egNum',null,true]]);
            $id = intval($_POST['logistics_id']);
             
            $logistics = $this->table('logistics')->where(['id'=>$id,'is_on'=>1])->get(['id'],true);
            if(!$logistics){
                $this->R('',70009);
            }
            $logistics = $this->table('logistics')->where(['id'=>$id])->delete();
            if(!$logistics){
                $this->R('',40001);
            }
            $this->R();
        }
        public function updateExpress(){
        $this->V(['logistics_number'=>['egNum',null,true]]);
        $id = $_POST['logistics_number'];
        $express = new \System\lib\Express\Express();
        $expressdetail = $express->getorder($id);
        $updateExpress = $this->table('logistics')->where(['logistics_number'=>$id])->update(['logistics_status'=>$expressdetail['state']]);
    }
	}
?>