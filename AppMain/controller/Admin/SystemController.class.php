<?php

namespace AppMain\controller\Admin;
use \System\BaseClass;

class SystemController extends CommonController {
    /**
     * 设置认购上限(百分数)
     */
    public function buyLimitEdit()
        {
            $this->checkPriv('test',0);
            $rule = [
                    'buy_limit'      =>['egNum'],
                ];
                $this->V($rule); 

                foreach ($rule as $k => $v) {
                        $data[$k] = $_POST[$k];
                }

            $data['update_time'] = time();
           

                $goods = $this->table('system')->where(['id'=>1])->update($data);
                if(!$goods){
                    $this->R('',40001);
                }
            $this->R(); 
        }
    /**
     *查询设置认购上限(百分数)
     */
    public function buyLimitDetail(){
        $buy_limit = $this->table('system')->where(['id'=>1])->get(['buy_limit'],true);
        if(!$buy_limit){
            $this->R('',70009);
        }
     $this->R(['buy_limit'=>$buy_limit]);

    }
    /**
     *查询设置B值
     */
    public function Bvalue(){
        $Bvalue = $this->table('system')->where(['id'=>1])->get(['Bvalue'],true);
        if(!$Bvalue){
            $this->R('',70009);
        }
     $this->R(['Bvalue'=>$Bvalue['Bvalue']]);

    }
    /**
     * 添加后台管理员
     */
    public function managerAdd(){
        $rule=[
            'manager_name'=>[],
            'manager_pwd'=>[],
        ];
        $this->V($rule);
        foreach ($rule as $k => $v) {
                $data[$k] = $_POST[$k];
        }
        if(isset($_POST['manager_phone'])){
            $rule=[
                'manager_phone'=>['mobile'],
            ];
            $this->V($rule);
            foreach ($rule as $k => $v) {
                    $data[$k] = $_POST[$k];
            }
                    }
        if(isset($_POST['manager_email'])){
            $rule=[
                'manager_email'=>['email'],
            ];
            $this->V($rule);
            foreach ($rule as $k => $v) {
                    $data[$k] = $_POST[$k];
            }
        }
        $data['manager_pwd'] =md5($data['manager_pwd']);
        $data['update_time']  = time();
        $data['add_time']     = time();
        $managerName=$this->H('Function')->isOnlyone('manager' ,'manager_name',$data['manager_name']);
        if($managerName!=NULL){
            $this->R('',70010);
        }
        $managerEmail=$this->H('Function')->isOnlyone('manager' ,'manager_email',$data['manager_email']);
        if($managerEmail!=NULL){
            $this->R('',70011);
        }
        $manager = $this->table('manager')->save($data);
        if(!$manager){
            $this->R('',40001);
        }
        $this->R();
    }
    /**
     * 查询所有的管理员信息
     */
    public function managerList(){
        $pageInfo = $this->P();
        //$field = ['id','manager_name','manager_email','manager_phone','manager_endlogin','add_time'];
        $class = $this->table('manager')->order('id desc');
        $manager = $this->getOnePageData($pageInfo,$class,'get','getListLength',null,false);
        if($manager){
            foreach ($manager as $k=>$v){
                $manager[$k]['manager_endlogin'] = long2ip($v['manager_endlogin']);
                $manager[$k]['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
            }
        }else{
            $manager=null;
        }
        $this->R(['manager'=>$manager,'pageInfo'=>$pageInfo]);
    }

    /**
     *查询一条管理员的信息 
     */
    public function managerOneDetail(){
        $managerId=intval($_POST['id']);
        $manager = $this->table('manager')->where(['id'=>$managerId])->get(['id','manager_name','manager_email','manager_phone','manager_level','manager_endlogin','add_time','last_ip','update_time'],true);
        $manager['manager_endlogin'] = long2ip($manager['manager_endlogin']);
        $manager['add_time'] = date('Y-m-d H:i:s',$manager['add_time']);
        $this->R(['manager'=>$manager]);

    }
    public function editUserPassword(){
        $rule=[
            'user_id'
        ];
        $this->V([]);
    }
    /**
     * 修改管理员信息
     */
    public function managerEdit(){
        $managerId=intval($_POST['id']);
        if(isset($_POST['manager_phone'])){
            $rule=[
                'manager_phone'=>['mobile'],
            ];
            $this->V($rule);
            foreach ($rule as $k => $v) {
                    $data[$k] = $_POST[$k];
            }
        }
        if(isset($_POST['manager_name'])){
            $rule=[
                'manager_name'=>[],
            ];
            $this->V($rule);
            foreach ($rule as $k => $v) {
                    $data[$k] = $_POST[$k];
            }
             $managerEmail=$this->H('Function')->isOnlyone('manager' ,'manager_name',$data['manager_name']);
        if($managerEmail!=NULL){
            $this->R('',70011);
        }
        }
        if(isset($_POST['manager_email'])){
            $rule=[
                'manager_email'=>['email'],
            ];
            $this->V($rule);
            foreach ($rule as $k => $v) {
                    $data[$k] = $_POST[$k];
            }
             $managerEmail=$this->H('Function')->isOnlyone('manager' ,'manager_email',$data['manager_email']);
        if($managerEmail!=NULL){
            $this->R('',70011);
        }
        }
        if(isset($_POST['manager_pwd'])){
            $rule=[
                'manager_pwd'=>[],
            ];
            $this->V($rule);
            $data['manager_pwd'] =md5($_POST['manager_pwd']);
        }
        
        $data['update_time']  = time();
       
        $manager = $this->table('manager')->where(['id'=>$managerId])->update($data);
        if(!$manager){
            $this->R('',40001);
        }
        $this->R();
    }
    /**
     * 删除一条或者多条管理员的信息
     */
    public function managerDelete(){
        $managerId=intval($_POST['id']);
        $manager = $this->table('manager')->where(['id'=>$managerId])->delete();
        if(!$manager){
            $this->R('',40001);
        }
        $this->R();
    }
    /**
     * 系统管理员用户角色列表
     */
    public function roleList(){
        $pageInfo=$this->P();
        $class=$this->table('manager_role')->order('id desc');
        $field=['id as role_id','role_name'];
        $roleList=$this->getOnePageData($pageInfo, $class, 'get','getListLength',[$field],false);
        
        if (!$roleList){
            $roleList=null;
        }
        $this->R(['roleList' => $roleList, 'pageInfo' => $pageInfo]);
    }
    
    /**
     * 获取系统管理员用户角色
     */
    public function getRoleDetail(){
        $this->V(['role_id'=>['egNum']]);
        $roleID=intval($_POST['role_id']);
        $role=$this->table('manager_role')->where(['id'=>$roleID])->get(['id','role_name','add_time','update_time'],true);
    
        if (!$role){
            $this->R('','70005');
        }
        $this->R(['role' => $role]);
    }
    
    /**
     * 编辑系统管理员用户角色
     */
    public function editRole(){
        $this->V(['role_id'=>['egNum'],'role_name'=>[]]);
        $roleID=intval($_POST['role_id']);
        $role=$this->table('manager_role')->where(['id'=>$roleID])->get(['id'],true);
        if (!$role){
            $this->R('','70005');
        }
        
        $role=$this->table('manager_role')->where(['id'=>$roleID])->update(['role_name'=>$_POST['role_name'],'update_time'=>time()]);
        if (!$role){
            $this->R('','40001');
        }
        
        $this->R(['role' => $role]);
    }
    
    /**
     * 添加系统管理员用户角色
     */
    public function addRole(){
        $this->V(['role_name'=>[]]);
        $role=$this->table('manager_role')->save(['role_name'=>$_POST['role_name'],'add_time'=>time()]);
        
        if (!$role){
            $this->R('','40001');
        }
        
        $this->R();
        
    }
    
    /**
     * 获取角色权限
     */
    public function getRoleAuth(){
        $this->V(['role_id'=>['egNum']]);
        $roleID=intval($_POST['role_id']);
        $list=$this->authList('return');
        $myAuth=$this->table('manager_role')->where(['id'=>$roleID])->get(['auth'],true);
        
        $auth=unserialize($myAuth['auth']);
        //dump($auth);//exit;
        if (!empty($auth)){
            foreach ($list as $key=>$a){
                foreach ($a['child'] as $key2=>$b){
                    foreach ($auth as $key3=>$c){
                        //dump($b['auth']['name']);
                        //dump($c['name']);
                        if ($b['auth']['name']==$c['name']){
                            $authdetail=[
                                'name'=>$c['name'],
                                '1'=>isset($c[1])?$c[1]:'0',
                                '2'=>isset($c[2])?$c[2]:'0',
                                '3'=>isset($c[3])?$c[3]:'0',
                                '4'=>isset($c[4])?$c[4]:'0',
                                '5'=>isset($c[5])?$c[5]:'0',
                            ];
                            $list[$key]['child'][$key2]['auth']=$authdetail;
                            unset($auth[$key3]);
                        }
                    }
                }
            }
        }
        //dump($auth);//exit;
        //dump($list);exit;

        $this->R(['authList'=>array_values($list)]);
    }
    
    /**
     * 设置角色权限
     */
    public function setRoleAuth(){
        $this->V(['auth'=>[],'role_id'=>['egNum']]);
        //dump($_POST['auth']);exit;
        $auth=[];
        foreach ($_POST['auth'] as $a){
            if (!empty($a['child'])){
                foreach ($a['child'] as $b){
                    $auth[]=[
                        'name'=>$b['auth']['name'],
                        '1'=>$b['auth'][1],
                        '2'=>$b['auth'][2],
                        '3'=>$b['auth'][3],
                        '4'=>$b['auth'][4],
                        '5'=>$b['auth'][5],
                    ];
                }
            }
        }
        
        $data=serialize($auth);
        $roleID=$_POST['role_id'];
        $myAuth=$this->table('manager_role')->where(['id'=>$roleID])->update(['auth'=>$data]);
        
        if (!$myAuth){
            $this->R('','40001');
        }
        $this->R();
    }
    
    /**
     * 权限列表
     */
    public function authList($type='json'){
        $authList=$this->table('manager_auth')->order('pid ASC, id ASC')->get(null);
        
        foreach ($authList as $key=>$a){
            if ($a['pid']==0){
                $a['child']=[];
                $list[$a['id']]=$a;
                unset($authList[$key]);
            }
        }
        
        foreach ($authList as $key=>$a){
            if ($a['pid']!=0){
                $a['auth']=[
                    'name'=>$a['mold'],
                    '1'=>'0',   
                    '2'=>'0',   
                    '3'=>'0',   
                    '4'=>'0',
                    '5'=>'0'
                ];
                $list[$a['pid']]['child'][]=$a;
            }
        }
        if ($type=='json'){
            $this->R(['authList'=>array_values($list)]);
        }
        else{
            return $list;
        }
    }
    
    /**
     * 添加权限
     */
    public function addAuth(){
        $rule=[
            'pid'=>['num'],
            'mold'=>[],
            'mold_name'=>[]
        ];
        $this->V($rule);
        
        $pid=intval($_POST['pid']);
        if ($pid > 0){
            $isMold=$this->table('manager_auth')->where(['id'=>$pid])->get(null,true);
            if (!$isMold){
                $this->R('','70009');
            }
        }
        
        $data=[
            'pid'=>$pid,
            'mold'=>$_POST['mold'],
            'mold_name'=>$_POST['mold_name']
        ];
        $add=$this->table('manager_auth')->save($data);
        if (!$add){
            $this->R('','40001');
        }
        
        $this->R();
    }
    
    /**
     * 编辑权限
     */
    public function editAuth(){
        $rule=[
            'id'=>['egNum'],
            'mold'=>[],
            'mold_name'=>[]
        ];
        $this->V($rule);
         
        $data=[
            'mold'=>$_POST['mold'],
            'mold_name'=>$_POST['mold_name']
        ];
        $update=$this->table('manager_auth')->where(['id'=>intval($_POST['id'])])->update($data);
        if (!$update){
            $this->R('','40001');
        }
         
        $this->R();
    }
    
    /**
     * 编辑权限
     */
    public function deleteAuth(){
        $rule=[
                'id'=>['egNum'],
        ];
        $this->V($rule);
    
        $moldID=intval($_POST['id']);
        
        $update=$this->table('manager_auth')->where('id='.$moldID.' or pid='.$moldID)->delete();
        if (!$update){
            $this->R('','40001');
        }
    
        $this->R();
    }
}