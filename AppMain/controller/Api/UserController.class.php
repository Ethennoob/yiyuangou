<?php
/**
 * 一元购系统---用户类
 * @authors 凌翔 (553299576@qq.com)
 * @date    2015-11-24 14:58:35
 * @version $Id$
 */

namespace AppMain\controller\Api;
use \System\BaseClass;

class UserController extends Baseclass {
    /**
     * checklogin
     */
    public function checklogin(){
            if (empty($_SESSION['userInfo']['openid'])) {
                $this->R('','90005');//跳//getOpenID
            }else{
                $this->R(['user_id'=>$_SESSION['userInfo']['userid']]);//进网站,返回user_id
            }
    }
    /**
     * 授权
     */
    public function getOpenID(){
        $refer = $_GET['refer'];
        $weObj = new \System\lib\Wechat\Wechat($this->config("WEIXIN_CONFIG"));
        $this->weObj = $weObj;
        if (empty($_GET['code']) && empty($_GET['state'])) {
            $callback = getHostUrl();
            $reurl = $weObj->getOauthRedirect($callback, "1");
            redirect($reurl, 0, '正在发送验证中...');
            exit(); 
        } elseif (intval($_GET['state']) == 1) {
                $accessToken = $weObj->getOauthAccessToken();
                $userInfo=$this->getUserInfo($accessToken);
                // 是否有用户记录
                $isUser = $this->table('user')->where(["openid" => $accessToken['openid'],"is_on"=>1])->get(null, true);
                /*var_dump($isUser);exit();*/
                
                if ($isUser==null) {
                    //没有此用户跳转至输入注册的页面
                    header("LOCATION:".getHost()."/register.html?refer=".$refer);
                }else{
                $userID=$isUser['id'];
                $updateUser = $this->table('user')->where(['is_on'=>1,'id'=>$userID])->update([
                    'last_login'=>time(),
                    'last_ip'=>ip2long(getClientIp()),
                    'nickname'=>$userInfo['nickname'],
                    'user_img'=>$userInfo['headimgurl']]
                    );
                $_SESSION['userInfo']=[
                    'openid'=>$isUser['openid'],
                    'userid'=>$isUser['id'],
                    'nickname'=>$isUser['nickname'],
                    'user_img'=>$isUser['user_img'],
                ];
                header("LOCATION:".$refer);//进入网站成功
                }
            }
        }
    /**
     * 微信个人中心菜单获取用户信息
     */
    public function getCenter(){
        $weObj = new \System\lib\Wechat\Wechat($this->config("WEIXIN_CONFIG"));
        $this->weObj = $weObj;
        if (empty($_GET['code']) && empty($_GET['state'])) {
            $callback = getHostUrl();
            $reurl = $weObj->getOauthRedirect($callback, "1");
            redirect($reurl, 0, '正在发送验证中...');
            exit(); 
        } elseif (intval($_GET['state']) == 1) {
                $accessToken = $weObj->getOauthAccessToken();
                 
                // 是否有用户记录
                $isUser = $this->table('user')->where(["openid" => $accessToken['openid'],'is_on'=>1])->get(null, true);
                /*var_dump($isUser);exit();*/
                
                if ($isUser==null) {
                    //没有此用户跳转至输入注册的页面
                    header("LOCATION:".getHost()."/register.html");
                }else{
                $userID=$isUser['id'];
                
                $updateUser = $this->table('user')->where(['id'=>$userID])->update(['last_login'=>time(),'last_ip'=>ip2long(getClientIp())]);
                $_SESSION['userInfo']=[
                    'openid'=>$isUser['openid'],
                    'userid'=>$isUser['id'],
                    'nickname'=>$isUser['nickname'],
                    'user_img'=>$isUser['user_img'],
                ];
                header("LOCATION:".getHost()."/personal.html");//进入网站成功
                }
            }
        }
    /**
     * 新用户从微信注册
     */
    public function getNewOpenID(){
        $refer = $_GET['refer'];
        $weObj = new \System\lib\Wechat\Wechat($this->config("WEIXIN_CONFIG"));
        $this->weObj = $weObj;
        if (empty($_GET['code']) && empty($_GET['state'])) {
            $callback = getHostUrl();
            $reurl = $weObj->getOauthRedirect($callback, "1");
            redirect($reurl, 0, '正在发送验证中...');
            exit(); 
        } elseif (intval($_GET['state']) == 1) {
                $accessToken = $weObj->getOauthAccessToken();
                    $mobile = $_GET['phone'];
                    $user = $this->table('user')->where(['is_on'=>1,'phone'=>$mobile])->get(['id'],true);
                    if(!$user){
                        //用户信息
                        $userInfo=$this->getUserInfo($accessToken);
                        $saveUser=$this->saveUser($userInfo,$mobile);//插入新会员数据
                        if (!$saveUser) {
                            $this->R('','40001');
                        }
                        //header("LOCATION:".getHost()."/Api/User/getOpenID");
                $userInfo=$this->getUserInfo($accessToken);
                // 是否有用户记录
                $isUser = $this->table('user')->where(["openid" => $accessToken['openid'],"is_on"=>1])->get(null, true);
                /*var_dump($isUser);exit();*/
                
                if ($isUser==null) {
                    //没有此用户跳转至输入注册的页面
                    header("LOCATION:".getHost()."/register.html?refer=".$_GET['refer']);
                }else{
                $userID=$isUser['id'];
                $updateUser = $this->table('user')->where(['id'=>$userID])->update([
                    'last_login'=>time(),
                    'last_ip'=>ip2long(getClientIp()),
                    'nickname'=>$userInfo['nickname'],
                    'user_img'=>$userInfo['headimgurl']]
                    );
                $_SESSION['userInfo']=[
                    'openid'=>$isUser['openid'],
                    'userid'=>$isUser['id'],
                    'nickname'=>$isUser['nickname'],
                    'user_img'=>$isUser['user_img'],
                ];
                header("LOCATION:".$refer);//进入网站成功
                   
                }

                }else{
                    $this->R('','70000');//手机已注册
                }
        }else{
            //用户取消授权
            $this->R('','90006');
        }
    }
    /**
     * 发送验证码
     */
    public function setCode(){
            $mobile = $_GET['phone'];
            $sendMessage = new \System\AppTools();
            $code= $sendMessage->generateMsgAuthCode();
            setcookie("verify",$code,time()+300,'/');
            $content = "您好！一元购注册的验证码为".$code;
            $sendMessage= $sendMessage->sendSms($mobile,$content);
            //$sendMessage= $sendMessage->sendSms(15521155161,$content);
            if (!$sendMessage) {
                $this->R('',40001);
            }
        }
    public function checkGetNum(){
        $time = strtotime(date("Y-m-d")."00:00:00");
        print_r($time);
    }
    /**
     * 查询手机号码是否已注册
     */
    public function checkPhone(){
        $this->V(['phone'=>['mobile']]);
        $mobile = intval($_POST['phone']);
        //验证码验证
            if (!isset($_COOKIE['verify'])) {
                //验证码已过期
                $this->R('','90009');
            }else{
                $this->V(['verify'=>[]]);
                $code = intval($_POST['verify']);
                if ($code!=$_COOKIE['verify']) {
                    //验证码错误
                    $this->R('','90008');
                }else{
        $user = $this->table('user')->where(['is_on'=>1,'phone'=>$mobile])->get(['id'],true);
            if($user){
                $this->R('','70000');//手机已注册
            }
        $this->R();
            }
        }
    }
    /**
     * 获取用户信息
     */
    private function getUserInfo($user){

        $user_info = $this->weObj->getOauthUserinfo($user['access_token'], $user['openid']);

        if (!$user_info){
            die("系统错误，请稍后再试！");
        }

        //是否关注
        $isFollow=$this->weObj->getUserInfo($user['openid']);
        if ($isFollow['subscribe']==1){
            $user_info['is_follow']=1;
        }
        else{
            $user_info['is_follow']=0;
        }

        return $user_info;
    }
    /**
     * 保存用户
     */
    private function saveUser($user_info,$mobile){

        $data = array(
            'openid' => $user_info['openid'],
            'phone' =>$mobile,
            'user_img' => $user_info['headimgurl'],
            'nickname' => $user_info['nickname'],
            'is_follow'=>$user_info['is_follow'],
            'add_time' => time()
        );
        $result=$this->table('user')->save($data);
        if (!$result){
            die("系统错误，请稍后再试！");
        }

        return $data;
    }
    
    /**
    *查看个人信息__一条数据
     */
    public function userOneAllDetail(){
    
        $this->V(['user_id'=>['egNum',null,true]]);

        $id = intval($_POST['user_id']);

            $user = $this->table('user')->where(['is_on'=>1,'id'=>$id])->get(['id','user_img','phone'],true);
            if(!$user){
                $this->R('',70009);
            }
            $status = $this->table('groupbuy_bill')->where(['is_on'=>1,'status'=>0,'user_id'=>$id])->get(['id'],false);
            $user['readypay'] = count($status);
            $status = $this->table('groupbuy_bill')->where(['is_on'=>1,'status'=>3,'user_id'=>$id])->get(['id'],false);
            $user['readydone'] = count($status);

            $this->R(['user'=>$user]);
    }
    /**
     * 修改个人信息
     */
    public function userOneEdit(){

       $rule = [
            'id'          =>['egNum'],
            'phone'       =>['mobile'],
        ];
        $this->V($rule);
        $id = intval($rule['id']);

        $user = $this->table('user')->where(['id'=>$id,'is_on'=>1])->get(['id'],true);
        if(!$user){
            $this->R('',70009);
        }

        unset($rule['id']);
        foreach ($rule as $k=>$v){
            if(isset($_POST[$k])){
                $data[$k] = $_POST[$k];
            }
        }

        $user['update_time'] = time();

        $user = $this->table('user')->where(['id'=>$id])->update($data);
        if(!$user){
            $this->R('',40001);
        }
        $this->R();
    }
    /**
     * 判定是否能去购买(微信支付之前的判断)
     */
    public function purchaseReady(){

        $rule = [
                    'goods_id'    =>['egNum'],
                    'user_id'     =>['egNum'],
                    'num'         =>['egNum'],
                ];
                $this->V($rule); 

            $goods_id    = $_POST['goods_id'];
            $user_id     = $_POST['user_id'];
            $num         = $_POST['num'];

            $good = $this->table('goods')->where(['id'=>$goods_id])->get(['limit_num','price'],true);
            if(!$good){
                $this->R('',90001);
            }
            //判断是否超过限购数
            if ($num>$good['limit_num']) {
                $this->R('',90001);
            }
            //判断是否卖完了
            $code = $this->table('code')->where(['goods_id'=>$goods_id,'is_use'=>0])->get(['code'],true);
            var_dump($code);exit();
            if(!$code){
                $this->R('',90001);
            }
            //判断是否已经买过并且超过限购数量
            $limit = $this->table('purchase')->where(['user_id'=>$user_id,'goods_id'=>$goods_id,'is_on'=>1])->get(['id'],false);
            $count = count($limit);
            if ($count+$num>$good['limit_num']) {
                $this->R('',90001);
            }
            
            $this->R(); 
    }   
    /**
     * 用户的购买记录(分页)     
     * user_id
     * 缩略图，商品标题，价格(总须人次)，已购买人次
     */
    public function purchaseList(){

        $this->V(['user_id'=>['egNum',null,true]]);
        $id = intval($_POST['user_id']);
        $pageInfo = $this->P();
        $file = ['id','goods_id','num'];

        $class = $this->table('record')->where(['is_on'=>1,'user_id'=>$id])->order('add_time desc');

        //查询并分页
        $detailpage = $this->getOnePageData($pageInfo,$class,'get','getListLength',[$file],false);
        if($detailpage ){
            foreach ($detailpage  as $k=>$v){
                $status = $this->table('goods')->where(['is_on'=>1,'id'=>$v['goods_id']])->get(['company_id','goods_name','limit_num','price','goods_thumb','goods_album'],true);
                $detailpage [$k]['goods_name'] = $status['goods_name'];
                $detailpage [$k]['limit_num'] = $status['limit_num'];
                $detailpage [$k]['price'] = $status['price'];
                $detailpage [$k]['total_num'] = $status['price'];
                if ($status['company_id']==37) {
                    $a = explode(';', $status['goods_album']);
                    $detailpage [$k]['goods_thumb'] = $a[0];
                }else{
                    $detailpage [$k]['goods_thumb'] = $status['goods_thumb'];
                }
                $a=explode(';', $status['goods_album']);
                $status = $this->table('purchase')->where(['is_on'=>1,'goods_id'=>$v['goods_id']])->get(['id'],false);
                $count = count($status);
                $detailpage [$k]['purchase_num'] = $count;
                $detailpage [$k]['last_num'] =$detailpage [$k]['total_num']-$count;
                if ($detailpage [$k]['last_num']==0) {
                    $status = $this->table('purchase')->where(['is_on'=>1,'goods_id'=>$v['goods_id']])->order('add_time desc')->limit(0,1)->get(['add_time'],true);
                if ($status!=null) {
                    ////究极无敌大判定////
                    $time = $status['add_time'];
                    $dataClass=$this->H('Roll');
                    $detailpage[$k]['lucky_time']=$dataClass->rollTime($time);
                }
                     $status = $this->table('bill')->where(['is_on'=>1,'goods_id'=>$v['goods_id']])->get(['user_id','code','add_time'],true);
                     //$detailpage[$k]['lucky_time'] = $status['add_time'];
                     //$detailpage[$k]['lucky_time'] =$status['add_time']+240;
                     $detailpage[$k]['code'] = $status['code'];
                     $status = $this->table('user')->where(['is_on'=>1,'id'=>$status['user_id']])->get(['nickname','user_img'],true);
                     $detailpage[$k]['nickname'] = $status['nickname'];
                     $detailpage[$k]['user_img'] = $status['user_img'];
                 } 
            }
        }else{
            $detailpage  = null;
        }
        //返回数据，参见System/BaseClass.class.php方法
        $this->R(['detailpage'=>$detailpage,'pageInfo'=>$pageInfo]);
    }
    /**
     * 购买记录中的商品详情
     * goods_id
     */
    public function purchaseOneDetail(){

        $rule = [
                    'goods_id'    =>['egNum'],
                    'user_id'     =>['egNum'],
                ];
                $this->V($rule); 

                foreach ($rule as $k => $v) {
                        $data[$k] = $_POST[$k];
                }

        $pageInfo = $this->P();
        $file = ['id','num','add_time'];

        $class = $this->table('record')->where(['is_on'=>1,'goods_id'=>$data['goods_id'],'user_id'=>$data['user_id']])->order('add_time desc');
        //$class2 = $this->table('record')->where(['is_on'=>1,'goods_id'=>$data['goods_id']])->order('add_time desc');
        //查询并分页
        $detailpage = $this->getOnePageData($pageInfo,$class,'get','getListLength',[$file],false);
        if($detailpage ){
            foreach ($detailpage  as $k=>$v){
                $detailpage [$k]['add_time'] = $v['add_time'];
                $status = $this->table('purchase')->where(['is_on'=>1,'goods_id'=>$data['goods_id'],'user_id'=>
                    $data['user_id'],'record_id'=>$v['id']])->get(['code'],false);
                $count = count($status);
                 for ($i=0; $i < $count; $i++) { 
                $v = implode(",",$status[$i]); //可以用implode将一维数组转换为用逗号连接的字符串
                $temp[] = $v;

            }
                $detailpage[$k]['code']=$temp;
                unset($temp);
                $status = $this->table('goods')->where(['is_on'=>1,'id'=>$data['goods_id']])->get(['goods_title','goods_thumb'],true);
                $detailpage [$k]['goods_title'] = $status['goods_title'];
                $detailpage [$k]['goods_thumb'] = $status['goods_thumb'];
                $status = $this->table('bill')->where(['is_on'=>1,'goods_id'=>$data['goods_id']])->get(['code','add_time','user_id','thematic_id'],true);
                $detailpage [$k]['lucky_code'] = $status['code'];
                $detailpage [$k]['lucky_time'] = $status['add_time'];
                $thematic = $this->table('thematic')->where(['is_on'=>1,'id'=>$status['thematic_id']])->get(['thematic_name'],true);
                $detailpage [$k]['thematic_name'] = $thematic['thematic_name'];
                $status = $this->table('user')->where(['is_on'=>1,'id'=>$status['user_id']])->get(['nickname'],true);
                $detailpage [$k]['nickname'] = $status['nickname'];
            }
        }else{

                $status = $this->table('goods')->where(['is_on'=>1,'id'=>$data['goods_id']])->get(['goods_title','goods_thumb'],true);
                $detailpage ['goods_title'] = $status['goods_title'];
                $detailpage ['goods_thumb'] = $status['goods_thumb'];
                $status = $this->table('bill')->where(['is_on'=>1,'goods_id'=>$data['goods_id']])->get(['code','add_time','user_id','thematic_id'],true);
                $detailpage ['lucky_code'] = $status['code'];
                $detailpage ['lucky_time'] = $status['add_time'];
                $thematic = $this->table('thematic')->where(['is_on'=>1,'id'=>$status['thematic_id']])->get(['thematic_name'],true);
                $detailpage ['thematic_name'] = $thematic['thematic_name'];
                $status = $this->table('user')->where(['is_on'=>1,'id'=>$status['user_id']])->get(['nickname'],true);
                $detailpage ['nickname'] = $status['nickname'];
                $this->R(['detailpage'=>$detailpage,'pageInfo'=>$pageInfo]);
        }

        //返回数据，参见System/BaseClass.class.php方法
        $this->R(['detailpage'=>$detailpage,'pageInfo'=>$pageInfo]);
    }
    /**
     * 获得商品记录列表(分页)
     */
    public function luckyList(){

        $this->V(['user_id'=>['egNum',null,true]]);
        $id = intval($_POST['user_id']);
        $pageInfo = $this->P();
        $file = ['id','goods_id','code','add_time'];

        $class = $this->table('bill')->where(['is_on'=>1,'user_id'=>$id,'is_confirm'=>1])->order('add_time desc');

        //查询并分页
        $luckypage = $this->getOnePageData($pageInfo,$class,'get','getListLength',[$file],false);
        if($luckypage ){
            foreach ($luckypage  as $k=>$v){
                $luckypage [$k]['lucky_time'] = $v['add_time'];
                $status = $this->table('goods')->where(['is_on'=>1,'id'=>$v['goods_id']])->get(['company_id','goods_name','price','nature','goods_thumb','goods_album'],true);
                $luckypage [$k]['goods_name'] = $status['goods_name'];
                $luckypage [$k]['nature'] = $status['nature'];
                $luckypage [$k]['total_num'] = $status['price'];
                if ($status['company_id']==37) {
                    $a = explode(';', $status['goods_album']);
                    $luckypage [$k]['goods_thumb'] = $a[0];
                }else{
                    $luckypage [$k]['goods_thumb'] = $status['goods_thumb'];
                }
                $status = $this->table('record')->where(['is_on'=>1,'goods_id'=>$v['goods_id'],'user_id'=>$id])->get(['num'],true);
                $luckypage [$k]['num'] = $status['num'];
                $status = $this->table('logistics')->where(['is_on'=>1,'bill_id'=>$v['id']])->get(['logistics_number'],true);
                $luckypage [$k]['logistics_number'] = $status['logistics_number'];
                unset($luckypage[$k]['add_time']);

            }
        }else{
            $luckypage  = null;
        }
        //返回数据，参见System/BaseClass.class.php方法
        $this->R(['obtained_goods'=>$luckypage,'pageInfo'=>$pageInfo]);
    }
    /**
     * 物流数据
     */
    public function getExpress(){
        $express = new \System\lib\Express\Express();
        $this->V(['logistics_number'=>['egNum',null,true]]);
        $id = $_POST['logistics_number'];
        $Express = $this->table('logistics_data')->where(['logistics_number'=>$id])->get(null,true);
        $expressdetail = json_decode($Express['data'], true);
        $this->R(['expressdetail'=>$expressdetail]);
       /* if (time()>=$Express['update_time']+24*3600 || $Express['data']==null) {
            echo "string";
            $expressdetail = $express->getorder($id);
            $Json = $express->getJson($id);
            if (@$expressdetail['state']!==null) {
            $data = array(
                'logistics_number' => $id,
                'data' =>$Json,
                'update_time' => time(),
                );
            //echo ($data['data']);exit();
            $record = $this->table('logistics_data')->where(['logistics_number'=>$id])->update($data);
            if (!$record) {
               $this->R('',40001);
            }
            $updateExpress = $this->table('logistics')->where(['logistics_number'=>$id])->update(['logistics_status'=>$expressdetail['state']]);
            if (!$updateExpress) {
               $this->R('',40001);
            }
                $this->R(['expressdetail'=>$expressdetail]);
            }else{
                $this->R(['expressdetail'=>null]);
            }
        }else{
            $result = $this->table('logistics_data')->where(['logistics_number'=>$id])->get(null,true);
            $expressdetail = json_decode($result['data'], true);
            $this->R(['expressdetail'=>$expressdetail]);
        }*/
    }
    public function getExpresstest(){
        $this->V(['logistics_number'=>['egNum',null,true]]);
        $id = $_POST['logistics_number'];
        $express = new \System\lib\Express\Express();
        $expressdetail = $express->getorder($id);
        
        $this->R(['expressdetail'=>$expressdetail]);
    }
    /////////////////////////////模拟购买数据接口/////////////////////勿删除
    /**
     * 购买商品(微信支付)
     */
    public function purchase(){

        $rule = [
                    'thematic_id' =>['egNum'],
                    'goods_id'    =>['egNum'],
                    'user_id'     =>['egNum'],
                    'num'         =>['egNum'],
                ];
                $this->V($rule); 

            $thematic_id = $_POST['thematic_id'];
            $goods_id    = $_POST['goods_id'];
            $user_id     = $_POST['user_id'];
            $num         = $_POST['num'];

            $good = $this->table('goods')->where(['id'=>$goods_id])->get(['limit_num'],true);
            if(!$good){
                $this->R('',70009);
            }
            //判断是否超过限购数
            if ($num>$good['limit_num']) {
                $this->R('',90001);
            }
            //判断是否卖完了
            $code = $this->table('code')->where(['goods_id'=>$goods_id,'is_use'=>0])->get(['id'],true);
            if(!$code){
                $this->R('',90001);
            }
            //判断是否已经买过并且超过限购数量
            $limit = $this->table('purchase')->where(['user_id'=>$user_id,'goods_id'=>$goods_id,'is_on'=>1])->get(['id'],false);
            $count = count($limit);
            if ($count+$num>$good['limit_num']) {
                $this->R('',90001);
            }
            //分配认购码给用户,生成购物流水单
            $roll = $this->generateCodeToUser($user_id,$goods_id,$thematic_id,$num);

            //生成购买记录
            $data = array(
                'goods_id' => $goods_id,
                'thematic_id' =>$thematic_id,
                'user_id' => $user_id,
                'num' => $num,
                'add_time' => time()
                );
            $record = $this->table('record')->save($data);
                if(!$record){
                    $this->R('',40001);
                }
            $this->R(); 
    }   
    /**
     * 分配认购码给用户
     * $user_id,$goods_id,$thematic_id,$num
     */
    private function generateCodeToUser($user_id,$goods_id,$thematic_id,$num){
        $codenum = $this->table('code')->where(['is_on'=>1,'is_use'=>0,'goods_id'=>$goods_id])->order("rand()")->limit($num)->get(['code'],false);
        $count = count($codenum);
            for ($i=0; $i < $count; $i++) { 
                //$code = $this->table('code')->where(['is_on'=>1,'is_use'=>0,'goods_id'=>$goods_id])->order("rand()")->get(['code'],true);
                $data['code'] = $codenum[$i]['code'];
                $data['user_id'] = $user_id;
                $data['goods_id'] = $goods_id;
                $data['thematic_id'] = $thematic_id;
                $data['add_time'] = time();
                $purchase = $this->table('purchase')->save($data);
                if(!$purchase){
                    $this->R('',40001);
                }
                $codeupdate = $this->table('code')->where(['code'=>$data['code']])->update(['is_use'=>1,'user_id'=>$user_id,'update_time'=>time()]);
                if(!$codeupdate){
                    $this->R('',40001);
                }
            }
    }
    
}