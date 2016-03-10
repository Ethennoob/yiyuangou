<?php

  namespace AppMain\controller\Api;
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
         $isRepeat=$this->table('record')->where(['wxPay_sn'=>$orderxml['out_trade_no']])->get(['id'],true);
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

         // 保存数据到数据库
        $this->V(['id'=>['egNum',null,false]]);
        $attach=json_decode($orderxml['attach'],true);
        $orderSn=$attach['goods_sn'];
        $price = $attach['price'];
        $isGoods = $this->table('goods')->where(array('goods_sn'=>$orderSn,'is_on'=>1))->get(['id','thematic_id'],true);
        $data['goods_id'] = $isGoods['id'];
        $data['thematic_id'] = $isGoods['thematic_id'];
        $isUser = $this->table('user')->where(array('openid'=>$orderxml['openid']))->get(['id'],true);
        $data['user_id'] = $isUser['id'];
        $data['wxPay_sn'] = $orderxml['out_trade_no'];
        $data['add_time'] = time();
        //$count = $this->table('code')->where(['is_on'=>1,'is_use'=>0,'goods_id'=>$data['goods_id']])->get(['code'],false);
        $data['num'] = $price;
        // $data['num'] = $orderxml['total_fee']/100;
        $data['ms_time'] = sprintf("%03d",floor(microtime()*1000));
        //开启事务
        //$this->table()->startTrans();
        $record = $this->table('record')->save($data);
        $record_id = $this->table('record')->where(array('wxPay_sn'=>$data['wxPay_sn'],'is_on'=>1))->get(['id','num'],true);

        /*$goods_id    = $data['goods_id'];
        $user_id     = $data['user_id'];
        $thematic_id     = $data['thematic_id'];
        $record_id = $record_id['id'];*/
        //分配认购码给用户,生成购物流水单
        $roll = $this->generateCodeToUser($data['user_id'],$data['goods_id'],$data['thematic_id'],$record_id['id']);
        
        $codenuma = $this->table('purchase')->where(['is_on'=>1,'record_id'=>$record_id['id'],'goods_id'=>$data['goods_id']])->get(['id'],false);
        if (count($codenuma) == $record_id['num']) {
          $this->table()->commit();
          //通知微信已支付成功
          $callback = $this->returnSuccess('支付成功！',$info);
          if (!$callback) {
            $this->table()->rollback();
            $this->R('',40001);
          }
          $this->table()->commit();
          $this->R(['callback'=>$data]);
          exit;
        }else{
          $this->table()->rollback();
          $this->R('',40001);
        }
        
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
