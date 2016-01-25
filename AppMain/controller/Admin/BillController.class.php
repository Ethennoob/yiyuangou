<?php 
	namespace AppMain\controller\Admin;
	use \System\BaseClass;
	/**
     * 订单class
     */
	class BillController extends BaseClass {

		/**
         * 中奖订单列表
         */
		public function billList(){
			$pageInfo = $this->P();
			if (isset($_POST['company_id'])) {
			$this->V(['company_id'=>['egNum',null,true]]);
            $id = intval($_POST['company_id']);
	        $class = $this->table('bill')->where(['is_on'=>1,'company_id'=>$id])->order('add_time desc');
	    }else{
	    	$class = $this->table('bill')->where(['is_on'=>1])->order('add_time desc');
	    }
	    $field = ['id','goods_id','user_id','thematic_id','is_confirm','status','bill_sn','add_time'];
	        //查询并分页
	        $bill = $this->getOnePageData($pageInfo,$class,'get','getListLength',[$field],false);
	        if($bill ){
	            foreach ($bill  as $k=>$v){
	                $bill [$k]['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
	                $status = $this->table('goods')->where(['is_on'=>1,'id'=>$v['goods_id']])->get(['goods_name','price'],true);
	                $bill [$k]['goods_name'] = $status['goods_name'];
	                $bill [$k]['price'] = $status['price'];
	                $status = $this->table('thematic')->where(['is_on'=>1,'id'=>$v['thematic_id']])->get(['thematic_name'],true);
	                $bill [$k]['thematic_name'] = $status['thematic_name'];
	                $status = $this->table('user')->where(['is_on'=>1,'id'=>$v['user_id']])->get(['nickname','phone','user_img'],true);
	                $bill [$k]['nickname'] = $status['nickname'];
	            }
	        }else{
	            $bill  = null;
	        }
	            $this->R(['bill'=>$bill,'pageInfo'=>$pageInfo]);
			}
		/**
         * 查询中奖订单列表（通过订单号）
         */
		public function billListSn(){
			$this->V(['bill_sn'=>['num']]);
        	$bill_sn = $_POST['bill_sn'];
        	 if (empty($_POST['company_id'])||!isset($_POST['company_id'])) {
                
             $where = 'is_on = 1 and bill_sn like "%'.$bill_sn.'%"';
        }else{
            $this->V(['company_id'=>['egNum',null,true]]);
            $id = $_POST['company_id'];
            $where='is_on = 1 and company_id = "'.$id.'" and bill_sn like "%'.$bill_sn.'%"';
        }
        	//$where = 'is_on = 1 and bill_sn like "%'.$bill_sn.'%"';
			$pageInfo = $this->P();
	        $field = ['id','goods_id','user_id','thematic_id','is_confirm','status','bill_sn','add_time'];
	        $class = $this->table('bill')->where($where)->order('add_time desc');

	        //查询并分页
	        $bill = $this->getOnePageData($pageInfo,$class,'get','getListLength',[$field],false);
	        if($bill ){
	            foreach ($bill  as $k=>$v){
	                $bill [$k]['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
	                $status = $this->table('goods')->where(['is_on'=>1,'id'=>$v['goods_id']])->get(['goods_name','price'],true);
	                $bill [$k]['goods_name'] = $status['goods_name'];
	                $bill [$k]['price'] = $status['price'];
	                $status = $this->table('thematic')->where(['is_on'=>1,'id'=>$v['thematic_id']])->get(['thematic_name'],true);
	                $bill [$k]['thematic_name'] = $status['thematic_name'];
	                $status = $this->table('user')->where(['is_on'=>1,'id'=>$v['user_id']])->get(['nickname','phone','user_img'],true);
	                $bill [$k]['nickname'] = $status['nickname'];
	            }
	        }else{
	            $bill  = null;
	        }
	            $this->R(['bill'=>$bill,'pageInfo'=>$pageInfo]);
		}
		/**
         * 查询中奖订单列表（通过订单号）
         */
		public function billListStatus(){
			$this->V(['status'=>['num']]);
        	$bill_sn = $_POST['status'];
        	 if (empty($_POST['company_id'])||!isset($_POST['company_id'])) {
                
             $where = 'is_on = 1 and status = "'.$bill_sn.'"';
        }else{
            $this->V(['company_id'=>['egNum',null,true]]);
            $id = $_POST['company_id'];
            $where='is_on = 1 and company_id = "'.$id.'" and status = "'.$bill_sn.'"';
        }
        	//$where = 'is_on = 1 and bill_sn like "%'.$bill_sn.'%"';
			$pageInfo = $this->P();
	        $field = ['id','goods_id','user_id','thematic_id','is_confirm','status','bill_sn','add_time'];
	        $class = $this->table('bill')->where($where)->order('add_time desc');

	        //查询并分页
	        $bill = $this->getOnePageData($pageInfo,$class,'get','getListLength',[$field],false);
	        if($bill ){
	            foreach ($bill  as $k=>$v){
	                $bill [$k]['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
	                $status = $this->table('goods')->where(['is_on'=>1,'id'=>$v['goods_id']])->get(['goods_name','price'],true);
	                $bill [$k]['goods_name'] = $status['goods_name'];
	                $bill [$k]['price'] = $status['price'];
	                $status = $this->table('thematic')->where(['is_on'=>1,'id'=>$v['thematic_id']])->get(['thematic_name'],true);
	                $bill [$k]['thematic_name'] = $status['thematic_name'];
	                $status = $this->table('user')->where(['is_on'=>1,'id'=>$v['user_id']])->get(['nickname','phone','user_img'],true);
	                $bill [$k]['nickname'] = $status['nickname'];
	            }
	        }else{
	            $bill  = null;
	        }
	            $this->R(['bill'=>$bill,'pageInfo'=>$pageInfo]);
		}
		/**
         * 查询中奖订单列表（通过用户名）
         */
		public function billListNickname(){
			$this->V(['name'=>[]]);
        	$name = $_POST['name'];
        	$where = 'is_on = 1 and nickname like "%'.$name.'%"';
        	$value = $this->table('user')->where($where)->limit(0,1)->order('add_time desc')->get(['id'],false);
        	//dump($value);
        	 if (empty($_POST['company_id'])||!isset($_POST['company_id'])) {
                
             $where1 = 'is_on = 1 and user_id = "'.$value[0]['id'].'"';
        }else{
            $this->V(['company_id'=>['egNum',null,true]]);
            $id = $_POST['company_id'];
            $where1='is_on = 1 and company_id = "'.$id.'" and user_id = "'.$value[0]['id'].'"';
        }
			$pageInfo = $this->P();
	        $field = ['id','goods_id','user_id','thematic_id','is_confirm','status','bill_sn','add_time'];
	        $class = $this->table('bill')->where($where1)->order('add_time desc');

	        //查询并分页
	        $bill = $this->getOnePageData($pageInfo,$class,'get','getListLength',[$field],false);
	        if($bill ){
	            foreach ($bill  as $k=>$v){
	                $bill [$k]['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
	                $status = $this->table('goods')->where(['is_on'=>1,'id'=>$v['goods_id']])->get(['goods_name','price'],true);
	                $bill [$k]['goods_name'] = $status['goods_name'];
	                $bill [$k]['price'] = $status['price'];
	                $status = $this->table('thematic')->where(['is_on'=>1,'id'=>$v['thematic_id']])->get(['thematic_name'],true);
	                $bill [$k]['thematic_name'] = $status['thematic_name'];
	                $status = $this->table('user')->where(['is_on'=>1,'id'=>$v['user_id']])->get(['nickname','phone','user_img'],true);
	                $bill [$k]['nickname'] = $status['nickname'];
	            }
	        }else{
	            $bill  = null;
	        }
	            $this->R(['bill'=>$bill,'pageInfo'=>$pageInfo]);
		}
		/**
         * 查询中奖订单列表（通过商品名稱）
         */
		public function billListGoodsName(){
			$this->V(['goods_name'=>[]]);
        	$name = $_POST['goods_name'];
        	$where = 'is_on = 1 and goods_name like "%'.$name.'%"';
        	$value = $this->table('goods')->where($where)->limit(0,1)->order('add_time desc')->get(['id'],false);
        	 if (empty($_POST['company_id'])||!isset($_POST['company_id'])) {
             $where1 = 'is_on = 1 and goods_id = "'.$value[0]['id'].'"';
        }else{
            $this->V(['company_id'=>['egNum',null,true]]);
            $id = $_POST['company_id'];
            $where1='is_on = 1 and company_id = "'.$id.'" and goods_id = "'.$value[0]['id'].'"';
        }
			$pageInfo = $this->P();
	        $field = ['id','goods_id','user_id','thematic_id','is_confirm','status','bill_sn','add_time'];
	        $class = $this->table('bill')->where($where1)->order('add_time desc');

	        //查询并分页
	        $bill = $this->getOnePageData($pageInfo,$class,'get','getListLength',[$field],false);
	        if($bill ){
	            foreach ($bill  as $k=>$v){
	                $bill [$k]['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
	                $status = $this->table('goods')->where(['is_on'=>1,'id'=>$v['goods_id']])->get(['goods_name','price'],true);
	                $bill [$k]['goods_name'] = $status['goods_name'];
	                $bill [$k]['price'] = $status['price'];
	                $status = $this->table('thematic')->where(['is_on'=>1,'id'=>$v['thematic_id']])->get(['thematic_name'],true);
	                $bill [$k]['thematic_name'] = $status['thematic_name'];
	                $status = $this->table('user')->where(['is_on'=>1,'id'=>$v['user_id']])->get(['nickname','phone','user_img'],true);
	                $bill [$k]['nickname'] = $status['nickname'];
	            }
	        }else{
	            $bill  = null;
	        }
	            $this->R(['bill'=>$bill,'pageInfo'=>$pageInfo]);
		}
		/**
         * 查询中奖订单列表（通过用户名）
         */
		public function billListThematicName(){
			$this->V(['thematic_name'=>[]]);
        	$name = $_POST['thematic_name'];
        	$where = 'is_on = 1 and thematic_name like "%'.$name.'%"';
        	$value = $this->table('thematic')->where($where)->limit(0,1)->order('add_time desc')->get(['id'],false);
        	//dump($value);
        	if (empty($_POST['company_id'])||!isset($_POST['company_id'])) {
             $where1 = 'is_on = 1 and thematic_id = "'.$value[0]['id'].'"';
        }else{
            $this->V(['company_id'=>['egNum',null,true]]);
            $id = $_POST['company_id'];
            $where1='is_on = 1 and company_id = "'.$id.'" and thematic_id = "'.$value[0]['id'].'"';
        }
			$pageInfo = $this->P();
	        $field = ['id','goods_id','user_id','thematic_id','is_confirm','status','bill_sn','add_time'];
	        $class = $this->table('bill')->where($where1)->order('add_time desc');

	        //查询并分页
	        $bill = $this->getOnePageData($pageInfo,$class,'get','getListLength',[$field],false);
	        if($bill ){
	            foreach ($bill  as $k=>$v){
	                $bill [$k]['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
	                $status = $this->table('goods')->where(['is_on'=>1,'id'=>$v['goods_id']])->get(['goods_name','price'],true);
	                $bill [$k]['goods_name'] = $status['goods_name'];
	                $bill [$k]['price'] = $status['price'];
	                $status = $this->table('thematic')->where(['is_on'=>1,'id'=>$v['thematic_id']])->get(['thematic_name'],true);
	                $bill [$k]['thematic_name'] = $status['thematic_name'];
	                $status = $this->table('user')->where(['is_on'=>1,'id'=>$v['user_id']])->get(['nickname','phone','user_img'],true);
	                $bill [$k]['nickname'] = $status['nickname'];
	            }
	        }else{
	            $bill  = null;
	        }
	            $this->R(['bill'=>$bill,'pageInfo'=>$pageInfo]);
		}
		/**
         * 查询中奖订单列表（通过用户名）
         */
		public function billListThematicPrice(){
			$this->V(['price'=>[]]);
        	$name = $_POST['price'];
        	$where = 'is_on = 1 and num = "'.$name.'"';
        	$value = $this->table('record')->where($where)->limit(0,1)->order('add_time desc')->get(['id'],false);
        	//dump($value);
        	if (empty($_POST['company_id'])||!isset($_POST['company_id'])) {
             $where1 = 'is_on = 1 and record_id = "'.$value[0]['id'].'"';
        }else{
            $this->V(['company_id'=>['egNum',null,true]]);
            $id = $_POST['company_id'];
            $where1='is_on = 1 and company_id = "'.$id.'" and record_id = "'.$value[0]['id'].'"';
        }
			$pageInfo = $this->P();
	        $field = ['id','goods_id','user_id','thematic_id','is_confirm','status','bill_sn','add_time'];
	        $class = $this->table('bill')->where($where1)->order('add_time desc');

	        //查询并分页
	        $bill = $this->getOnePageData($pageInfo,$class,'get','getListLength',[$field],false);
	        if($bill ){
	            foreach ($bill  as $k=>$v){
	                $bill [$k]['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
	                $status = $this->table('goods')->where(['is_on'=>1,'id'=>$v['goods_id']])->get(['goods_name','price'],true);
	                $bill [$k]['goods_name'] = $status['goods_name'];
	                $bill [$k]['price'] = $status['price'];
	                $status = $this->table('thematic')->where(['is_on'=>1,'id'=>$v['thematic_id']])->get(['thematic_name'],true);
	                $bill [$k]['thematic_name'] = $status['thematic_name'];
	                $status = $this->table('user')->where(['is_on'=>1,'id'=>$v['user_id']])->get(['nickname','phone','user_img'],true);
	                $bill [$k]['nickname'] = $status['nickname'];
	            }
	        }else{
	            $bill  = null;
	        }
	            $this->R(['bill'=>$bill,'pageInfo'=>$pageInfo]);
		}
		/**
         * 查询一条订单记录
         */
		public function billOneDetail(){
			$this->V(['bill_id'=>['egNum',null,true]]);
            $id = intval($_POST['bill_id']);
            //查询一条数据
            $bill = $this->table('bill')->where(['is_on'=>1,'id'=>$id])->get(null,true);
            if(!$bill){
                $this->R('',70009);
            }
             
	                $bill['add_time'] = date('Y-m-d H:i:s',$bill['add_time']);
	                $status = $this->table('goods')->where(['is_on'=>1,'id'=>$bill['goods_id']])->get(['goods_name','goods_thumb','cost_price','price','goods_sn'],true);
	                $bill['goods_name'] = $status['goods_name'];
	                $bill['goods_thumb'] = $status['goods_thumb'];
	                $bill['price'] = $status['price'];
	                $bill['cost_price'] = $status['cost_price'];
	                $bill['goods_sn'] = $status['goods_sn'];
	                $status = $this->table('thematic')->where(['is_on'=>1,'id'=>$bill['thematic_id']])->get(['thematic_name','nature'],true);
	                $bill['thematic_name'] = $status['thematic_name'];
	                $bill['nature'] = $status['nature'];
	                $status = $this->table('user')->where(['is_on'=>1,'id'=>$bill['user_id']])->get(['nickname','phone','user_img'],true);
	                $bill['nickname'] = $status['nickname'];
	                $bill['phone'] = $status['phone'];
                    $bill['user_img'] = $status['user_img'];
	            
            $this->R(['bill'=>$bill]);
		}

		/**
	     *删除一条订单数据（设置数据库字段为0，相当于回收站）
	     */
	    public function billDelete(){
	    
	        $this->V(['bill_id'=>['egNum',null,true]]);
	        $id = intval($_POST['bill_id']);
	         
	        $bill = $this->table('bill')->where(['id'=>$id,'is_on'=>1])->get(['id'],true);
	    
	        if(!$bill){
	            $this->R('',70009);
	        }
	    
	        $bill = $this->table('bill')->where(['id'=>$id])->update(['is_on'=>0]);
	        if(!$bill){
	            $this->R('',40001);
	        }
	        $this->R();
	    }

	    /**
	     *删除一条订单数据（清除数据）
	     */
	    public function billDeleteConfirm(){
	    
	        $this->V(['bill_id'=>['egNum',null,true]]);
	        $id = intval($_POST['bill_id']);
	         
	        $bill = $this->table('bill')->where(['id'=>$id,'is_on'=>1])->get(['id'],true);
	    
	        if(!$bill){
	            $this->R('',70009);
	        }
	        $bill = $this->table('bill')->where(['id'=>$id])->delete();
	        if(!$bill){
	            $this->R('',40001);
	        }
	        $this->R();
	    }

	    /**
         * 确认订单
         */
	    public function billConfirm(){
	    
	        $this->V(['bill_id'=>['egNum',null,true]]);
	        $id = intval($_POST['bill_id']);
	         
	        $bill = $this->table('bill')->where(['id'=>$id,'is_on'=>1,'is_confirm'=>0])->get(['id'],true);
	    
	        if(!$bill){
	            $this->R('',70009);
	        }
	    
	        $bill = $this->table('bill')->where(['id'=>$id])->update(['is_confirm'=>1,'is_cancel'=>0,'status'=>1]);
	        if(!$bill){
	            $this->R('',40001);
	        }
	        //发送消息通知用户
	        $bills = $this->table('bill')->where(['id'=>$id])->get(['user_id','goods_id','thematic_id'],true);
	        if(!$bills){
	            $this->R('',40001);
	        }
	        $user = $this->table('user')->where(['id'=>$bills['user_id']])->get(['openid','phone'],true);
	        $openid = $user['openid'];
	        $phone = $user['phone'];
	        $goods = $this->table('goods')->where(['id'=>$bills['goods_id']])->get(['goods_name'],true);
	        $goods_name = $goods['goods_name'];
	        $thematic = $this->table('thematic')->where(['id'=>$bills['thematic_id']])->get(['thematic_name'],true);
	        $thematic_name = $thematic['thematic_name'];
	        $content = "尊敬的一团云购用户，恭喜您抽中了".$thematic_name."商品".$goods_name."，请及时查看并完善您的收货地址信息，方便我们为您送货。谢谢配合。";
	        //微信公众号提醒
	        $weObj = new \System\lib\Wechat\Template($this->config("WEIXIN_CONFIG"));
			$weObj->setTemplate($openid,$content);
			//手机短信提醒
			$sendMessage = new \System\AppTools();
			$sendMessage= $sendMessage->sendSms($phone,$content);
	        $this->R();
	    }
	    /**
         * 发货
         */
	    public function billPost(){
	    
	        $this->V(['bill_id'=>['egNum',null,true]]);
	        $id = intval($_POST['bill_id']);
	         
	        $bill = $this->table('bill')->where(['id'=>$id,'is_on'=>1,'is_post'=>0])->get(['id'],true);
	    
	        if(!$bill){
	            $this->R('',70009);
	        }
	    
	        $bill = $this->table('bill')->where(['id'=>$id])->update(['is_post'=>1,'status'=>2]);
	        if(!$bill){
	            $this->R('',40001);
	        }
	        
	        $this->R();
	    }

	    /**
         * 取消订单
         */
	    public function billCancel(){
	    
	        $this->V(['bill_id'=>['egNum',null,true]]);
	        $id = intval($_POST['bill_id']);
	         
	        $bill = $this->table('bill')->where(['id'=>$id,'is_on'=>1,'is_cancel'=>0])->get(['id'],true);
	    
	        if(!$bill){
	            $this->R('',70009);
	        }
	    
	        $bill = $this->table('bill')->where(['id'=>$id])->update(['is_cancel'=>1,'is_confirm'=>0,'status'=>0]);
	        if(!$bill){
	            $this->R('',40001);
	        }
	        $this->R();
	    }
	    public function outputExcel(){
	    	$data = array(
	    		'title' =>"一元购中奖订单明细",
	    		'thematic_name' => "专题名称",
	    		'bill_sn'    =>"订单编号",
	    		'goods_name'  =>"商品名",
	    		'goods_sn'  =>"商品编号",
	    		'code'  =>"中奖认购码",
	    		'price' =>"商品价格",
	    		'nickname' =>"获奖用户昵称",
	    		'phone'   =>"联系电话",
	    		'add_time'  =>"时间",
	    		);

	    	$bills = $this->table('bill')->where(['is_on'=>1,'is_confirm'=>1])->order('add_time asc')->get(null,false);
	    	if($bills){

	    		for ($i=0; $i <count($bills); $i++) { 
	    			$bills[$i]['add_time'] = date('Y-m-d H:i:s',$bills[$i]['add_time']);
	                $status = $this->table('goods')->where(['is_on'=>1,'id'=>$bills[$i]['goods_id']])->get(['goods_name','price','goods_sn'],true);
	                $bills[$i]['goods_name'] = $status['goods_name'];
	                $bills[$i]['price'] = $status['price'];
	                $bills[$i]['goods_sn'] = $status['goods_sn'];
	                $status = $this->table('thematic')->where(['is_on'=>1,'id'=>$bills[$i]['thematic_id']])->get(['thematic_name'],true);
	                $bills[$i]['thematic_name'] = $status['thematic_name'];
	                $status = $this->table('user')->where(['is_on'=>1,'id'=>$bills[$i]['user_id']])->get(['nickname','phone'],true);
	                $bills[$i]['nickname'] = $status['nickname'];
	                $bills[$i]['phone'] = $status['phone'];
	    		}
	        }else{
	            $bills  = null;
	        }
	    	$outputExcel = $this->H('Excel')->PHPExcel($bills,$data);
	    	if (!$outputExcel) {
	    		$this->R('',40001);
	    	}
	    }
	    public function phone(){
	    	$code = rand(100000,999999);
	    	$_SESSION['code'] =$code;
	    	$content = "您好！一元购注册的验证码为".$_SESSION['code'];
	    	$sendMessage = new \System\AppTools();
	    	$code= $sendMessage->generateMsgAuthCode();
	    	dump($code);exit;
	    	$sendMessage= $sendMessage->sendSms(15521155161,$content);
	    	if (!$sendMessage) {
	    		$this->R('',40001);
	    	}
	    }

	}

 ?>