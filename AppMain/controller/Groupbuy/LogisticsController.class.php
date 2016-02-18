<?php
	namespace AppMain\controller\Groupbuy;
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

	        $rule = [
                    'logistics_number'  =>[],
                    'logistics_name'    =>[],
                ];
            $this->V($rule);
            foreach ($rule as $k => $v) {
            	$data[$k] = $_POST[$k];
            }
            $userAddr = $this->table('groupbuy_bill')->where(['id'=>$billId])->get(['address_id','goods_id'],true);
            if (!$userAddr) {
                $userAddr['address_id'] =null;
            }
            $data = array(
            		'logistics_number'  =>$data['logistics_number'],
                    'logistics_name'    =>$data['logistics_name'],
                    'logistics_status'  =>0,
                    'bill_id'           =>$billId,
                    'user_address_id'   =>$userAddr['address_id'],
                    'add_time'          =>time(),
            	);
            $logistics = $this->table('groupbuy_logistics')->save($data);
            if (!$logistics) {
            	$this->R('',40001);
            }
            $dataA = array(
                    'logistics_number'  =>$data['logistics_number'],
                    'update_time'       =>time(),
                );
            //开启事务
            $this->table()->startTrans();
            $logistics = $this->table('logistics_data')->save($dataA);
            if (!$logistics) {
                $this->R('',40001);
            }
            //设置bill表is_post状态为1
            $billPost = $this->table('groupbuy_bill')->where(['id'=>$billId])->update(['status'=>3,'post_time'=>time()]);
            if (!$billPost) {
                $this->R('',40001);
            }
            //向快递100发送订阅请求
            $express = new \System\lib\Express\Express();
            $post_data = $express->getJson($data['logistics_number']);
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
            $p = curl_exec($ch);
            if ($p) {
                $this->table()->commit();//提交事务
            }else{
                $this->table()->rollback();//回滚事务
            }

		}

		/**
	     * 修改物流单
	     */
		public function logisticsEdit(){
			$this->V(['logistics_id'=>['egNum',null,true]]);
	        $logisticsId = intval($_POST['logistics_id']);
	        $rule = [
	        		'logistics_id'                =>[],
                	'logistics_number'  =>[],
                    'logistics_name'    =>[],
                    //'logistics_status'  =>[],
            ];
            $this->V($rule);
            $logistics = $this->table('groupbuy_logistics')->where(['id'=>$logisticsId,'is_on'=>1])->get(['id'],true);
            if(!$logistics){
                $this->R('',70009);
            }

            unset($rule['id']);
            foreach ($rule as $k=>$v){
                if(isset($_POST[$k])){
                    $data[$k] = $_POST[$k];
                }
            }

            $logistics = $this->table('groupbuy_logistics')->where(['id'=>$logisticsId])->update($data);
            if(!$logistics){
                $this->R('',40001);
            }
            $dataA = array(
                    'logistics_number'  =>$data['logistics_number'],
                    'update_time'       =>time(),
                );
            //开启事务
            $this->table()->startTrans();
            $logistics = $this->table('logistics_data')->save($dataA);
            if (!$logistics) {
                $this->R('',40001);
            }
            //向快递100发送订阅请求
            $express = new \System\lib\Express\Express();
            $post_data = $express->getJson($data['logistics_number']);
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
            $p = curl_exec($ch);
            if ($p) {
                $this->table()->commit();//提交事务
            }else{
                $this->table()->rollback();//回滚事务
            }
            //$this->R();
		}

		/**
         * 物流列表
         */
        public function logisticsList(){
            $pageInfo = $this->P();

            $class = $this->table('groupbuy_logistics')->where(['is_on'=>1])->order('add_time desc');

            //查询并分页
            $logisticslist = $this->getOnePageData($pageInfo,$class,'get','getListLength',null,false);
            if($logisticslist){
                foreach ($logisticslist as $k=>$v){
                    $logisticslist[$k]['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
                    $logisticslist[$k]['update_time'] = date('Y-m-d H:i:s',$v['update_time']);
                    $status = $this->table('groupbuy_bill')->where(['is_on'=>1,'id'=>$v['bill_id']])->get(['bill_sn'],true);
                    $logisticslist[$k]['bill_sn'] = $status['bill_sn'];
                    $this->getExpress($v['logistics_number']);
                }
            }else{
                $logisticslist = null;
            }
            $this->R(['logisticslist'=>$logisticslist,'pageInfo'=>$pageInfo]);
        }
        /**
     * 物流数据
     */
    private function getExpress($id){
        $express = new \System\lib\Express\Express();
        $Express = $this->table('logistics_data')->where(['logistics_number'=>$id])->get(null,true);
        $data   = json_decode($Express['data'], true);
        $updateExpress = $this->table('groupbuy_logistics')->where(['logistics_number'=>$id])->update(['logistics_status'=>@$data['lastResult']['state'],'uptate_time'=>time()]);
            if (!$updateExpress) {
               $this->R('',40001);
            }
        return true;
    }

        /**
         * 查询一条物流信息
         */
        public function logisticsOneDetail(){
            $this->V(['logistics_id'=>['egNum',null,true]]);
	        $logisticsId = intval($_POST['logistics_id']);
                //查询一条数据
                $logistics = $this->table('groupbuy_logistics')->where(['is_on'=>1,'id'=>$logisticsId])->get(null,true);
                if(!$logistics){
                    $this->R('',70009);
                }
                //查询一条数据
                $status = $this->table('groupbuy_bill')->where(['is_on'=>1,'id'=>$logistics['bill_id']])->get(['bill_sn'],true);
                $logistics['bill_sn'] = $status['bill_sn'];
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
         * 模糊查询物流列表(通过物流单号)
         */
        public function logisticsListNumber(){
            $pageInfo = $this->P();
            $this->V(['logistics_number'=>['num']]);
            $logistics_number = $_POST['logistics_number'];   
            $where='is_on = 1 and logistics_number like "%'.$logistics_number.'%"';
            $class = $this->table('groupbuy_logistics')->where($where)->order('add_time desc');
            //查询并分页
            $logisticslist = $this->getOnePageData($pageInfo,$class,'get','getListLength',null,false);
            if($logisticslist){
                foreach ($logisticslist as $k=>$v){
                    $logisticslist[$k]['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
                    $logisticslist[$k]['update_time'] = date('Y-m-d H:i:s',$v['update_time']);
                    $status = $this->table('groupbuy_bill')->where(['is_on'=>1,'id'=>$v['bill_id']])->get(['bill_sn'],true);
                    $logisticslist[$k]['bill_sn'] = $status['bill_sn'];
                    $this->getExpress($v['logistics_number']);
                }
            }else{
                $logisticslist = null;
            }
            $this->R(['logisticslist'=>$logisticslist,'pageInfo'=>$pageInfo]);
        }
        /**
         * 模糊查询物流列表(通过订单号)
         */
        public function logisticsListBillsn(){
            $pageInfo = $this->P();
            $this->V(['bill_sn'=>['num']]);
            $bill_sn = $_POST['bill_sn'];   
            $value = $this->table('groupbuy_bill')->where(['is_on'=>1,'bill_sn'=>$bill_sn])->limit(0,1)->order('add_time desc')->get(['id'],true);
            $class = $this->table('groupbuy_logistics')->where(['is_on'=>1,'bill_id'=>$value['id']])->order('add_time desc');
            //查询并分页
            $logisticslist = $this->getOnePageData($pageInfo,$class,'get','getListLength',null,false);
            if($logisticslist){
                foreach ($logisticslist as $k=>$v){
                    $logisticslist[$k]['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
                    $logisticslist[$k]['update_time'] = date('Y-m-d H:i:s',$v['update_time']);
                    $status = $this->table('groupbuy_bill')->where(['is_on'=>1,'id'=>$v['bill_id']])->get(['bill_sn'],true);
                    $logisticslist[$k]['bill_sn'] = $status['bill_sn'];
                    $this->getExpress($v['logistics_number']);
                }
            }else{
                $logisticslist = null;
            }
            $this->R(['logisticslist'=>$logisticslist,'pageInfo'=>$pageInfo]);
        }
        /**
         * 模糊查询物流列表(通过物流公司名)
         */
        public function logisticsListName(){
            $pageInfo = $this->P();
            $this->V(['logistics_name'=>[]]);
            $logistics_name = $_POST['logistics_name'];   
            $where='is_on = 1 and logistics_name like "%'.$logistics_name.'%"';
            $class = $this->table('groupbuy_logistics')->where($where)->order('add_time desc');
            //查询并分页
            $logisticslist = $this->getOnePageData($pageInfo,$class,'get','getListLength',null,false);
            if($logisticslist){
                foreach ($logisticslist as $k=>$v){
                    $logisticslist[$k]['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
                    $logisticslist[$k]['update_time'] = date('Y-m-d H:i:s',$v['update_time']);
                    $status = $this->table('groupbuy_bill')->where(['is_on'=>1,'id'=>$v['bill_id']])->get(['bill_sn'],true);
                    $logisticslist[$k]['bill_sn'] = $status['bill_sn'];
                    $this->getExpress($v['logistics_number']);
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
            $pageInfo = $this->P();
            $this->V(['status'=>['num']]);
            $status = $_POST['status'];   
            $where='is_on = 1 and logistics_status like "%'.$status.'%"';
            $class = $this->table('groupbuy_logistics')->where($where)->order('add_time desc');
            //查询并分页
            $logisticslist = $this->getOnePageData($pageInfo,$class,'get','getListLength',null,false);
            if($logisticslist){
                foreach ($logisticslist as $k=>$v){
                    $logisticslist[$k]['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
                    $logisticslist[$k]['update_time'] = date('Y-m-d H:i:s',$v['update_time']);
                    $status = $this->table('groupbuy_bill')->where(['is_on'=>1,'id'=>$v['bill_id']])->get(['bill_sn'],true);
                    $logisticslist[$k]['bill_sn'] = $status['bill_sn'];
                    $this->getExpress($v['logistics_number']);
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
             
            $logistics = $this->table('groupbuy_logistics')->where(['id'=>$id,'is_on'=>1])->get(['id'],true);
        
            if(!$logistics){
                $this->R('',70009);
            }
        
            $logistics = $this->table('groupbuy_logistics')->where(['id'=>$id])->update(['is_on'=>0]);
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
             
            $logistics = $this->table('groupbuy_logistics')->where(['id'=>$id,'is_on'=>1])->get(['id'],true);
            if(!$logistics){
                $this->R('',70009);
            }
            $logistics = $this->table('groupbuy_logistics')->where(['id'=>$id])->delete();
            if(!$logistics){
                $this->R('',40001);
            }
            $this->R();
        }
        
	}
?>