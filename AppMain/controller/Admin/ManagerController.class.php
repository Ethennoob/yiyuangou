<?php
    namespace AppMain\controller\Admin;
    use \System\BaseClass;
    /**
     * 后台超级管理员和管理员特权class
     */
    class ManagerController extends BaseClass{
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
            if($_POST['manager_phone']){
                $rule=[
                    'manager_phone'=>['mobile'],
                ];
                $this->V($rule);
                foreach ($rule as $k => $v) {
                        $data[$k] = $_POST[$k];
                }
            }
            if($_POST['manager_email']){
                $rule=[
                    'manager_email'=>['email'],
                ];
                $this->V($rule);
                foreach ($rule as $k => $v) {
                        $data[$k] = $_POST[$k];
                }
            }
            $data['manager_pwd'] =md5($data['manager_pwd']);
            // $data['parent_id'] =$_POST['parent_id'];
            $data['manager_level'] =$_POST['manager_level'];
            $data['update_time']  = time();
            $data['add_time']     = time();
            $data['is_on']     = $_POST['is_on'];
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
            $field = ['id','manager_name','manager_email','manager_phone','manager_level','manager_endlogin','add_time'];
            $class = $this->table('manager')->order('id desc');
            $manager = $this->getOnePageData($pageInfo,$class,'get','getListLength',[$field],false);
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
    }
?>    