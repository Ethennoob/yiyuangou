<?php

namespace AppMain\controller\Admin;
use \System\BaseClass;
abstract class CommonController extends BaseClass {

    public function __construct() {
        if (!isset($_SESSION['adminInfo']['manager_id'])) {
        	$this->R('', '70002');
        }
        
        $this->user=[
        	'adminid' => $_SESSION['adminInfo']['manager_id'],
        	'name' => $_SESSION['adminInfo']['manager_name'],
        	'roleid'=>$_SESSION['adminInfo']['manager_role']
        ];
    }

    /**
     * TODO
     * 检查当前角色对此模块的指定操作是否有指定权限
     * @param string $subCode    模块ID  
     * @param type $privid       1:可读; 2:添加;3:编辑;4:删除;5:执行;0:没权限; 
     */
    public function checkPriv($mold, $privid) {
        if ($this->user['roleid']===1){		//超级管理员跳过权限
        	return;
        }
    	
        $roleID=$this->user['roleid'];
        $key='Auth'.$mold.$roleID;
        $flat=false;
        
        //读取该角色对应模块权限
        $auth=$this->S()->get($key);
        if (!$auth){
        	//读取数据库
        	$role=$this->table('manager_role')->where(['id'=>$roleID])->get(['auth',true]);
        	if (!$role){
        		$this->R('','70005');
        	}
        	
        	$role=unserialize($role);
        	
        	$auth=isset($role['auth'][$mold])?$role['auth'][$mold]:false;
        	if (!$auth){
        		$this->S()->set($key, 'no authority' , 3600*24);
        		$this->R('','70006');
        	}
        	
        	$flat=true;
        	
        }
        elseif ($auth=='no authority'){
        	$this->R('','70006');
        }
        //????
        if ($auth[$privid]==true){
        	if ($flat){
        		$this->S()->set($key, $auth , 3600*24);
        	}
        	
        	return;
        }
        else{
        	$this->R('','70006');
        }        
    }


    
}
