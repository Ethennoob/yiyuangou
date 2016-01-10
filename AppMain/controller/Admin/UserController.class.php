<?php


namespace AppMain\controller\Admin;
use \System\BaseClass;

class UserController extends Baseclass {
	/**
	 * 后台用户列表(分页)
	 * id，昵称，电话，微信头像，最后一次登录时间，添加时间
	 */
	public function userList(){
        $where=['is_on'=>1];
        //$this->queryFilter，拼接查询字段
        $whereFilter=$this->queryFilter($where,['is_show']);

        $pageInfo = $this->P();
        $file = ['id','nickname','phone','user_img','last_login','add_time'];

        $class = $this->table('user')->where($whereFilter)->order('add_time desc');

        //查询并分页
        $userpage = $this->getOnePageData($pageInfo,$class,'get','getListLength',[$file],false);
        if($userpage ){
            foreach ($userpage  as $k=>$v){
                $userpage [$k]['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
                $userpage [$k]['last_login'] = date('Y-m-d H:i:s',$v['last_login']);
            }
        }else{
            $userpage  = null;
        }
        //返回数据，参见System/BaseClass.class.php方法
        $this->R(['userpage'=>$userpage,'pageInfo'=>$pageInfo]);
    }
    /**
     * 用户个人信息,收货信息
     * 收货人，收货手机，收货地址，邮编
     */
    public function userOneDetail(){
    	$this->V(['user_id'=>['egNum',null,true]]);

        $id = intval($_POST['user_id']);
        $address = $this->table('user_address')->where(['is_on'=>1,'user_id'=>$id,'is_default'=>1])->get(['id'],true);
        if(!$address){
            $userinfo = $this->table('user')->where(['is_on'=>1,'id'=>$id])->get(null,true);
            $userinfo ['add_time'] = date('Y-m-d H:i:s',$userinfo['add_time']);
            $userinfo ['update_time'] = date('Y-m-d H:i:s',$userinfo['update_time']);
            $userinfo ['last_ip'] = long2ip($userinfo['last_ip']);
            $userinfo ['last_login'] = date('Y-m-d H:i:s',$userinfo['last_login']);
            $this->R(['userinfo'=>$userinfo]);
        }
        $where = 'A.id='.$id.' and A.is_on = 1 and B.is_default = 1 and B.is_on = 1';
        $dataClass = $this->H('UserInfo');
        $userinfo = $dataClass->getUserInfo($where);
        if(!$userinfo){
            $this->R('',70009);
        }

        $userinfo ['last_ip'] = long2ip($userinfo['last_ip']);
        $userinfo ['last_login'] = date('Y-m-d H:i:s',$userinfo['last_login']);
        $userinfo ['add_time'] = date('Y-m-d H:i:s',$userinfo['add_time']);
        $userinfo ['update_time'] = date('Y-m-d H:i:s',$userinfo['update_time']);
        //返回数据，参见System/BaseClass.class.php方法
        $this->R(['userinfo'=>$userinfo]);
    }
    /**
     * 通过手机号查询用户信息
     */
    public function userOnePhone(){
        $this->V(['mobile'=>[]]);
        $phone = intval($_POST['mobile']);
        $pageInfo = $this->P();
        $where = 'is_on = 1 and phone like "%'.$phone.'%"';
        
        $file = ['id','nickname','phone','user_img','last_login','add_time'];
        $class = $this->table('user')->where($where)->order('add_time desc');
        //查询并分页
        $userpage = $this->getOnePageData($pageInfo,$class,'get','getListLength',[$file],false);
        if($userpage ){
            foreach ($userpage  as $k=>$v){
                $userpage [$k]['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
                $userpage [$k]['last_login'] = date('Y-m-d H:i:s',$v['last_login']);
            }
        }else{
            $userpage  = null;
        }
        //返回数据，参见System/BaseClass.class.php方法
        $this->R(['userpage'=>$userpage,'pageInfo'=>$pageInfo]);
    }
    /**
     * 查询列表（用户是否关注）
     */
    public function userListNickname(){
        $this->V(['nickname'=>[]]);
        $nickname = $_POST['nickname'];
        $pageInfo = $this->P();
        $file = ['id','nickname','phone','user_img','last_login','add_time'];
        $where = 'is_on = 1 and nickname like "%'.$nickname.'%"';
        $class = $this->table('user')->where($where)->order('add_time desc');

        //查询并分页
        $userpage = $this->getOnePageData($pageInfo,$class,'get','getListLength',[$file],false);
        if($userpage ){
            foreach ($userpage  as $k=>$v){
                $userpage [$k]['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
                $userpage [$k]['last_login'] = date('Y-m-d H:i:s',$v['last_login']);
            }
        }else{
            $userpage  = null;
        }
        //返回数据，参见System/BaseClass.class.php方法
        $this->R(['userpage'=>$userpage,'pageInfo'=>$pageInfo]);
    }
    /**
     * 查询列表（用户是否关注）
     */
    public function userListFollow(){
        $this->V(['is_follow'=>['num',null,true]]);
        $is_follow = intval($_POST['is_follow']);
        $pageInfo = $this->P();
        $file = ['id','nickname','phone','user_img','last_login','add_time'];

        $class = $this->table('user')->where(['is_on'=>1,'is_follow'=>$is_follow])->order('add_time desc');

        //查询并分页
        $userpage = $this->getOnePageData($pageInfo,$class,'get','getListLength',[$file],false);
        if($userpage ){
            foreach ($userpage  as $k=>$v){
                $userpage [$k]['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
                $userpage [$k]['last_login'] = date('Y-m-d H:i:s',$v['last_login']);
            }
        }else{
            $userpage  = null;
        }
        //返回数据，参见System/BaseClass.class.php方法
        $this->R(['userpage'=>$userpage,'pageInfo'=>$pageInfo]);
    }
    /**
     * 查询列表（用户是否冻结）
     */
    public function userListFroze(){
        $this->V(['is_froze'=>['num',null,true]]);
        $is_froze = intval($_POST['is_froze']);
        $pageInfo = $this->P();
        $file = ['id','nickname','phone','user_img','last_login','add_time'];

        $class = $this->table('user')->where(['is_on'=>1,'is_froze'=>$is_froze])->order('add_time desc');

        //查询并分页
        $userpage = $this->getOnePageData($pageInfo,$class,'get','getListLength',[$file],false);
        if($userpage ){
            foreach ($userpage  as $k=>$v){
                $userpage [$k]['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
                $userpage [$k]['last_login'] = date('Y-m-d H:i:s',$v['last_login']);
            }
        }else{
            $userpage  = null;
        }
        //返回数据，参见System/BaseClass.class.php方法
        $this->R(['userpage'=>$userpage,'pageInfo'=>$pageInfo]);
    }

    /**
     * 修改一条用户信息
     * 电话,收货人，收货手机，收货地址，邮编
     */
    public function userOneEdit(){
    	$rule = [
    	    'id'              =>['egNum'],
            'phone'           =>['mobile'],
        ];
        $this->V($rule);
        $id = intval($_POST['id']);

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
       $data['update_time']     = time();
        $user = $this->table('user')->where(['id'=>$id])->update($data);
        if(!$user){
            $this->R('',40001);
        }

        $this->R();
    }
    /*
    删除一条会员信息数据（设置数据库字段为0，相当于回收站）
     */
    public function userOneDelete(){
    
        $this->V(['user_id'=>['egNum',null,true]]);
        $id = intval($_POST['user_id']);
         
        $user = $this->table('user')->where(['id'=>$id,'is_on'=>1])->get(['id'],true);
    
        if(!$user){
            $this->R('',70009);
        }
    
        $user = $this->table('user')->where(['id'=>$id])->update(['is_on'=>0]);
        if(!$user){
            $this->R('',40001);
        }

        $this->R();
    }
    /**
     *删除一条用户信息（清除数据）
     */
    public function userOneDeleteconfirm(){
    
        $this->V(['user_id'=>['egNum',null,true]]);
        $id = intval($_POST['user_id']);
         
        $user = $this->table('user')->where(['id'=>$id,'is_on'=>1])->get(['id'],true);
    
        if(!$user){
            $this->R('',70009);
        }

        $user = $this->table('user')->where(['id'=>$id])->delete();
        if(!$user){
            $this->R('',40001);
        }

        $this->R();
    }
}