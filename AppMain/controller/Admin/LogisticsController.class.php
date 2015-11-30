<?php
	namespace AppMain\controller\Admin;
	use \System\BaseClass;
	/**
     * 物流类
     */
	class LogisticsController extends BaseClass {
		/**
	     * 添加物流单
	     */
		public function logisticsAdd(){
			$this->V(['id'=>['egNum',null,true]]);
			//获取订单id
	        $billId = intval($_POST['id']);

	        $this->V(['user_id'=>['egNum',null,true]]);
	        //获取用户id
	        $userId = intval($_POST['user_id']);

	        $rule = [
                    'logistics_number'  =>[],
                    'logistics_name'    =>[],
                    'logistics_status'  =>[],
                ];
            $this->V($rule);
            foreach ($rule as $k => $v) {
            	$data[$k] = $_POST[$k];
            }
            $userAddr = $this->table('user')->where(['id'=>$userId])->get(['user_address_id'],true);
            $data = array(
            		'logistics_number'  =>$data['logistics_number'],
                    'logistics_name'    =>$data['logistics_name'],
                    'logistics_status'  =>1,
                    'bill_id'           =>$billId,
                    'user_address_id'   =>$userAddr['user_address_id'],
                    'add_time'          =>time(),
            	);
            $logistics = $this->table('logistics')->save($data);
            if (!$logistics) {
            	$this->R('',40001);
            }
            //设置bill表is_post状态为1
            $billPost = $this->table('bill')->where(['id'=>$billId])->update(['is_post'=>1]);
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
                    'logistics_status'  =>[],
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
            $where=['is_on'=>1];
            $pageInfo = $this->P();
            $class = $this->table('logistics')->where($where)->order('add_time desc');
            //查询并分页
            $logisticslist = $this->getOnePageData($pageInfo,$class,'get','getListLength',null,false);
            if($logisticslist){
                foreach ($logisticslist as $k=>$v){
                    $logisticslist[$k]['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
                    $logisticslist[$k]['update_time'] = date('Y-m-d H:i:s',$v['update_time']);
                }
            }else{
                $logisticslist = null;
            }
            $this->R(['logisticslist'=>$logisticslist,'pageInfo'=>$pageInfo]);
        }

        /**
         * 查询一条物流信息
         */
        public function logisticsOneList(){
            $this->V(['id'=>['egNum',null,true]]);
	        $logisticsId = intval($_POST['id']);
            if (!$logisticsDetail){
                //查询一条数据
                $logistics = $this->table('logistics')->where(['is_on'=>1,'id'=>$logisticsId])->get(null,true);
                if(!$logistics){
                    $this->R('',70009);
                }
                $logistics['update_time'] = date('Y-m-d H:i:s',$logistics['update_time']);
                $logistics['add_time'] = date('Y-m-d H:i:s',$logistics['add_time']);
            }    
            $this->R(['logistics'=>$logistics]);
        }

        /**
         *删除一条物流数据（设置数据库字段为0，相当于回收站）
         */
        public function logisticsDelete(){
        
            $this->V(['id'=>['egNum',null,true]]);
            $id = intval($_POST['id']);
             
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
            $this->V(['id'=>['egNum',null,true]]);
            $id = intval($_POST['id']);
             
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
	}
?>