<?php

  namespace AppMain\controller\Groupbuy;
  use \System\BaseClass;
  /**
    * 服务器循环调用
    */
  class LoopController extends BaseClass {
  	/**
  	 * 每隔一分钟
  	 * 判断此商品是否该下架
  	 * 没成团（人不够）的都组团失败
  	 * 订单取消
  	 */
  	public function loop(){
  		/*ignore_user_abort();//即使Client断开(如关掉浏览器)，PHP脚本也可以继续执行.
  		//set_time_limit(0);//执行时间为无限制，php默认的执行时间是30秒，通过set_time_limit(0)可以让程序无限制的执行下去
  		$interval=60*5;//每隔5分钟运行
  	do{
  		//
  		sleep($interval);//按设置的时间等待5分钟循环执行*/
  		$time = $this->table('groupbuy_goods')->where(['is_on'=>1,'is_show'=>1])->get(['id','add_time','group_time'],false);
        for ($i=0; $i < count($time); $i++) { 
        	$temptime = $time[$i]['add_time']+$time[$i]['group_time']*3600;
        	if (time()>$temptime) {
        		//商品下架
        		$goods = $this->table('groupbuy_goods')->where(['is_on'=>1,'is_show'=>1,'id'=>$time[$i]['id']])->update(['is_show'=>0]);
        		//未组团成功的全部失败（人数不够）
        		$groups = $this->table('groupbuy_groups')->where(['is_on'=>1,'status'=>0,'goods_id'=>$time[$i]['id']])->update(['status'=>1]);
        		//订单状态改为取消
        		$bill = $this->table('groupbuy_bill')->where(['is_on'=>1,'status'=>0,'goods_id'=>$time[$i]['id']])->update(['status'=>5]);
        		$bill = $this->table('groupbuy_bill')->where(['is_on'=>1,'status'=>1,'goods_id'=>$time[$i]['id']])->update(['status'=>5]);
        		//订单退款（元翔）
        		//
        		
        	}
        }
        //}while(true);
  	}
  }
 ?>