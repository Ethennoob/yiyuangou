<?php

  namespace AppMain\controller\Groupbuy;
  use \System\BaseClass;
  /**
    * 微信回调类
    */
  class CallbackController extends BaseClass {
     
     /**
      * 微信支付回调处理接口(j接口要保持可以访问，微信才会发数据包过来)
      */
     public function order(){
         //格式化post_data
         $orderxml=$_POST;
         if (!$orderxml) {
             $postStr = file_get_contents("php://input");
             if (!empty($postStr)) {
                 $orderxml = (array)simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
              } else return false;
         }
         
         //记录日志
         $info=json_encode($orderxml);
         $this->payLog($info);
         
         //验证是否重复
         $isRepeat=$this->table('groupbuy_record')->where(['wxPay_sn'=>$orderxml['out_trade_no']])->get(['id'],true);
         if($isRepeat){
             $this->returnFail("重复流水号！",$info);
         }

         /* 调用微信支付验证签名类 */
         $this->vendor('WxPay.JsApi_pub');
         $notify = new \Notify_pub();       
         $this->notify=$notify;
         //存储微信的回调
         $xml = $GLOBALS['HTTP_RAW_POST_DATA'];
         $notify->saveData($xml);
          
         //验证签名
         if($notify->checkSign() == FALSE) $this->returnFail("签名失败",$info);

         //查找订单
        $attach=json_decode($orderxml['attach'],true);
        $orderSn=$attach['bill_sn'];
        $bill = $this->table('groupbuy_bill')->where(array('bill_sn'=>$orderSn,'is_on'=>1))->get(null,true);

        if(!$bill) $this->returnFail("订单不存在！",$info);
        if($bill['status'] >= 1) $this->returnFail("订单已支付！");

        //开启事务
        // $this->table()->startTrans();

        //开团订单
       if ($bill['type']==1) {
        $data = array(
              'goods_id' => $bill['goods_id'],
              'leader' => $bill['user_id'],
              'add_time' => time()
              );
        $group = $this->table('groupbuy_groups')->save($data);
        if(!$group) $this->returnFail("插入失败！",$info);
        //拿取刚刚新建的团数据
          $groupId = $this->table('groupbuy_groups')->where(['is_on'=>1,'leader'=>$bill['user_id']])->get(['id'],true);
            if(!$group) $this->returnFail("查询失败！",$info);
            $data = array(
                'group_id' => $groupId['id'],
                'user_id' => $bill['user_id'],
                'add_time' => time()
                );
          $group_member = $this->table('groupbuy_group_member')->save($data);
          if(!$group_member) $this->returnFail("插入失败！",$info);
            $bills = $this->table('groupbuy_bill')->where(['is_on'=>1,'id'=>$bill['id']])->update(['group_id'=>$groupId['id']]);
        if(!$bills) $this->returnFail("插入失败！",$info);

            //生成record
            $recorddata = array(
                'bill_id' => $bill['id'],
                'group_id' => $groupId['id'],
                'goods_id' => $bill['goods_id'],
                'user_id' => $bill['user_id'],
                'wxPay_sn'=>$orderxml['out_trade_no'],
                'add_time' => time()
                );
            $record = $this->table('groupbuy_record')->save($recorddata);
            if(!$record) $this->returnFail("插入失败！",$info);
          //参团订单
        }elseif($bill['type']==2){
          $data = array(
                'group_id' => $bill['group_id'],
                'user_id' => $bill['user_id'],
                'add_time' => time()
                );
          $group_member = $this->table('groupbuy_group_member')->save($data);
          if(!$group_member) $this->returnFail("插入失败！",$info);
            //2-2
            $member = $this->table('groupbuy_group_member')->where(['is_on'=>1,'group_id'=>$bill['group_id']])->get(['id'],false);
            if (count($member)==2) {
              $group = $this->table('groupbuy_groups')->where(['is_on'=>1,'id'=>$bill['group_id']])->update(['soft'=>$bill['user_id'],'update_time'=>time()]);
              if(!$group) $this->returnFail("插入失败！",$info);
            }elseif (count($member)==3) {
              $group = $this->table('groupbuy_groups')->where(['is_on'=>1,'id'=>$bill['group_id']])->update(['stool'=>$bill['user_id'],'update_time'=>time()]);
              if(!$group) $this->returnFail("插入失败！",$info);
            }
            //生成record
            $data = array(
                'bill_id' => $id,
                'group_id' => $bill['group_id'],
                'goods_id' => $bill['goods_id'],
                'user_id' => $bill['user_id'],
                'wxPay_sn'=>$orderxml['out_trade_no'],
                'add_time' => time()
                );
            $record = $this->table('groupbuy_record')->save($data);
            if(!$record) $this->returnFail("插入失败！",$info);
        }
        //更新订单
        $bills = $this->table('groupbuy_bill')->where(['is_on'=>1,'id'=>$bill['id']])->update(['status'=>1,'pay_time'=>time(),'update_time'=>time()]);
        if(!$bills) $this->returnFail("订单已支付！",$info);
          $this->table()->commit();
          //通知微信已支付成功
          $callback = $this->returnSuccess('支付成功！',$info);
          if (!$callback) {
            //$this->table()->rollback();
            $this->returnFail('支付失败！',$info);
          }
          //$this->table()->commit();
          exit;
     }

     /**
       * 分配认购码给用户
       * $user_id,$goods_id,$thematic_id,$num
       */
      private function generateCodeToUser($user_id,$goods_id,$thematic_id,$record_id){
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
    }

     /**
      * 返回错误给微信
      * @param unknown $return_msg
      * @param unknown $info
      */
     private function returnFail($return_msg,$info){
        $this->errorPayLog($return_msg,$info);
      $this->notify->setReturnParameter("return_code","FAIL");//返回状态码
      $this->notify->setReturnParameter("return_msg",$return_msg);//返回信息
          
      echo $this->notify->returnXml();
      exit;
     }
     
     /**
      * 返回成功给微信
      * @param unknown $return_msg
      * @param unknown $info
      */
     private function returnSuccess($return_msg,$info){
        $this->successPayLog($return_msg,$info);
        $this->notify->setReturnParameter("return_code","SUCCESS");
        $this->notify->setReturnParameter("return_msg","OK");
        echo $this->notify->returnXml();
        exit;
     }
     
     /**
      * 记录日志
      * @param str $info
      */
     private function payLog($info){
         $path=__ROOT__.'/Log/payLog/'.date('Ymd').'/';
         if(dirExists($path)){
             file_put_contents($path."log.txt", date("Y-m-d H:i:s") . "-----------". PHP_EOL . $info . PHP_EOL. '------------------ '.PHP_EOL, FILE_APPEND);
         }
     }
     
     
     /**
      * 记录错误支付日志(文件)
      * @param str $info
      */
     private function errorPayLog($return_msg,$info){
        $path=__ROOT__.'/Log/errorPayLog/'.date('Ymd').'/';
        if(dirExists($path)){
          file_put_contents($path."log.txt", date("Y-m-d H:i:s") . "-----------". PHP_EOL . $info . PHP_EOL.'-----------' . PHP_EOL .'结果：'.$return_msg. PHP_EOL. '------------------ '.PHP_EOL, FILE_APPEND);
        }
     }
     
     /**
      * 记录成功支付日志(文件)
      * @param str $info
      */
     private function successPayLog($return_msg,$info){
      $path=__ROOT__.'/Log/successPayLog/'.date('Ymd').'/';
      if(dirExists($path)){
        file_put_contents($path."log.txt", date("Y-m-d H:i:s") . "-----------". PHP_EOL . $info . PHP_EOL.'-----------' .PHP_EOL.'结果：'.$return_msg. PHP_EOL. '------------------ '.PHP_EOL, FILE_APPEND);
      }
     }
  }
