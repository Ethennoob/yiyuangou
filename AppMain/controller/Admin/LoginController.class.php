<?php
    namespace AppMain\controller\Admin;
    use \System\BaseClass;
    /**
     * 后台登录class
     */
    class LoginController extends BaseClass{
        /**
         * 登checkAdminLogin
         */
        public function checkAdminLogin(){
            if(empty($_SESSION['adminInfo']['manager_id'])){
                
                    $this->R('',70002);//没登录，跳转到login登录
                
            }      
        }
        /**
         * 登录操作
         */
        public function login(){
            if(isset($_SESSION['manager_id'])&&$_SESSION['manager_id']>0){
                $this->R('',80000);//你已经登陆了
            }                
            $post=[
                'manager_name'=>$_POST['manager_name'],
                'manager_pwd'=>$_POST['manager_pwd'],
                
            ];
            $this->V(['manager_name'=>[],'manager_pwd'=>[]],$post);
            $manager = $this->table('manager')->where(['manager_name'=>$post['manager_name'],'manager_pwd'=>md5($post['manager_pwd'])])->get(null,true);
            if(!$manager){
                $this->R('',70001);
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
                    $this->R('','80000');//你已经登陆了
            }
            if (empty($_COOKIE['OneBuy-AUTOLOGIN'])){
                    $this->R('','70004');
            }
            $token=$_COOKIE['OneBuy-AUTOLOGIN'];
            setcookie('OneBuy-AUTOLOGIN','1',time()-3600,'/');  //删除cookie
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
            $_SESSION['adminInfo']['manager_id'] = $manager['id'];
            /*$_SESSION['adminInfo']['manager_name']=$manager['manager_name'];
            $_SESSION['role_base_id']=$manager['role_base_id'];
            $_SESSION['adminInfo']['autoLogin']=$loginStatus;
*/
            if ($isAutoLogin==1){
                    $expire=60 * 60 * 24 * 7;
                    $timeout = time() + $expire;
                    $token=md5(uniqid(rand(), TRUE));

                   /* $autoLogin=[
                                    'manager_id'=> $manager['manager_id'],
                                    //'identifier' => $manager['identifier'],
                                    'timeout' => $timeout
                    ];*/
                    //$this->S()->set($token,$autoLogin,60*60*24*7);
                    setcookie('OneBuy-AUTOLOGIN', $token,$timeout,'/');
            }

            //更新用户信息
            $data=[
                    'last_ip'=>getClientIp(),
                    'manager_endlogin'=>time()	
            ];
            $this->table('manager')->where(['id'=>$manager['id']])->update($data);
            $this->R();
        }
        /**
         * 注销
         */
        public function logout(){
            session_destroy();
            header("LOCATION:".getHost()."/admin/login.html");//跳转到login登录
            
        }
    }
?>    