<?php
    namespace AppMain\controller\Admin;
    use \System\BaseClass;
    /**
     * 后台登录class
     */
    class LoginController extends BaseClass{
        /**
         * 登录操作
         */
        public function login(){
            if(isset($_SESSION['manager_id'])&&$_SESSION['manager_id']>0){
                $this->R('',80000);
            }                
            $post=[
                'manager_name'=>$_POST['manager_name'],
                'manager_password'=>$_POST['manager_password'],
            ];
            $this->V(['manager_name'=>[],'manager_password'=>[]],$post);
            $manager = $this->table('manager')->where(['manager_name'=>$post['manager_name'],'manager_password'=>md5($post['manager_password'])])->get(null,true);
            if(!$manager){
                $this->R('',70000);
            }
            if (isset($_POST['is_remember'])&&$_POST['is_remember']==1){
                $isRemember=1;
            }
            else{
                $isRemember=0;
            }
            $this->loginAction($manager,0,$isRemember);
            
        }

        /**
         * 自动登录操作
         */
        public function autoLogin(){
            if (isset($_SESSION['manager_id'])&&$_SESSION['manager_id'] > 0){
                    $this->R('','80000');
            }
            if (empty($_COOKIE['OneTrade-AUTOLOGIN'])){
                    $this->R('','70004');
            }
            $token=$_COOKIE['OneTrade-AUTOLOGIN'];
            setcookie('OneTrade-AUTOLOGIN','1',time()-3600,'/');  //删除cookie
            //查找token
            $isToken=$this->S()->get($token);
            if (!$isToken){
                    $this->R('','70004');
            }
            //检查信息
            $manager= $this->table('manager')->where(['id'=>$isToken['manager_id']])->get(null,true);
            if (!$manager){
                    $this->R('','70004');
            }
            if ($manager['identifier'] != $isToken['identifier']){
                    $this->R('','70004');
            }
            if ($isToken['timeout'] < time()){
                    $this->R('','70004');
            }
            //$this->S()->delete($token);
            $this->loginAction($manager,1,1);
        }

        /**
         * 登录动作
         */
        private function loginAction($manager,$loginStatus,$isAutoLogin){
            $_SESSION['manager_id'] = $manager['id'];
            $_SESSION['manager_name']=$manager['manager_name'];
            $_SESSION['role_base_id']=$manager['role_base_id'];
            $_SESSION['autoLogin']=$loginStatus;

            if ($isAutoLogin==1){
                    $expire=60 * 60 * 24 * 7;
                    $timeout = time() + $expire;
                    $token=md5(uniqid(rand(), TRUE));

                    $autoLogin=[
                                    'manager_id'=> $manager['manager_id'],
                                    'identifier' => $manager['identifier'],
                                    'timeout' => $timeout
                    ];
                    //$this->S()->set($token,$autoLogin,60*60*24*7);
                    setcookie('OneTrade-AUTOLOGIN', $token,$timeout,'/');
            }

            //更新用户信息
            $data=[
                    'last_ip'=>getClientIp(),
                    'manager_endlogin'=>time()	
            ];
            $this->table('manager')->where(['id'=>$manager['id']])->update($data);
            $this->R();
        }
    }
?>    