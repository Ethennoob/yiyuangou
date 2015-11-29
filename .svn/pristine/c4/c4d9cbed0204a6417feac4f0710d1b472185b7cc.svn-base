<?php
namespace AppMain\controller\Task;
use \AppMain\controller\Task\CommonController;
class IndexController extends CommonController {
	
	/**
     * 下载图像
     */
    public function downloadHeadImg() {
    	ignore_user_abort(true);
        set_time_limit(0);
        
    	for ($i=100;$i>0;$i++){
    		$rt=$this->table('task_headimgdownload')->where(array('status'=>0))->get();
    		if (!$rt) {
    			$this->setTimeOut($i);
    			continue;
    		}
    		$tempPath='../TempImg/';
    		$path='img/headImg/';
    		
    		foreach ($rt as $a){
    			//先验证是否已更换图片
    			$user=$this->table('user')->where(array('id'=>$a['user_id']))->get(array('photo'),true);
    			if ($user['photo']!=$a['photo']) {
    				$status=3;
    			}
    			else{
    				$img=httpGet($a['photo']);
    				$fileName=time().rand(10000,99999).'.jpg';
    				 
    				$save=file_put_contents($tempPath.$fileName, $img);
    				
    				if ($save){
    				    $uploadStatus=true;
    				    
    					$image=new \System\lib\Image\Image($tempPath.$fileName);
    					$fileName2=str_replace('.jpg', '_150.jpg', $fileName);
    					$image->thumb(200,10000)->save($tempPath.$fileName2);
    					$status=1;
    					
    					//上传到图片服务器
    					$upload=httpPost($this->config('IMG_UPLOAD'), ['file'=>new \CURLFile(realpath($tempPath.$fileName)),'savePath'=>$path,'saveName'=>basename($tempPath.$fileName)],true);
    					$upload=json_decode($upload,true);
    					if (!($upload&&$upload['result']==true)){
    					    $uploadStatus=false;
    					}
    					
    					
    					
    					//上传压缩图
    					$upload2=httpPost($this->config('IMG_UPLOAD'), ['file'=>new \CURLFile(realpath($tempPath.$fileName2)),'savePath'=>$path,'saveName'=>basename($tempPath.$fileName2)],true);
    					$upload2=json_decode($upload,true);
    					if (!($upload2&&$upload2['result']==true)){
    					    $uploadStatus=false;
    					}
    					
    					if ($uploadStatus==false){
    					    $status=2;
    					}
    					else{
    					    $up2=$this->table('user')->where(['id'=>$a['user_id']])->update(['photo'=>'/',$path.$fileName,'photo_150'=>'/',$path.$fileName2]);
    					    $status=1;
    					}
    				}
    				else{
    				    $status=2;
    				}
    			}
    			$up=$this->table('task_headimgdownload')->where(['id'=>$a['id']])->update(array('status'=>$status,'execute_time'=>time()));
    		}
    	}
    	
    	
    	
    }
    
    private function setTimeOut($i){
    	sleep(60);
    }  

    public function test(){
    	dump(preg_match('/http:\/\/wx.qlogo.cn\/[^>]*$/si', '/img/headImg/143574911038548.jpg'));
    }
    
    public function test1(){
        /** 确保这个函数只能运行在SHELL中 */
        if (substr(php_sapi_name(), 0, 3) !== 'cli') {
            die("This Programe can only be run in CLI mode");
        }
         
        /**  关闭最大执行时间限制, 在CLI模式下, 这个语句其实不必要 */
        set_time_limit(0);
         
        $pid  = posix_getpid(); //取得主进程ID
        $user = posix_getlogin(); //取得用户名
         
        echo <<<EOD
USAGE: [command | expression]
input php code to execute by fork a new process
input quit to exit
	  
        Shell Executor version 1.0.0 by laruence
EOD;
         
        while (true) {
             
            $prompt = "\n{$user}$ ";
            $input  = readline($prompt);
             
            readline_add_history($input);
            if ($input == 'quit') {
                break;
            }
            $this->process_execute($input . ';');
        }
         
        exit(0);
         
        
    }
    
    function process_execute($input) {
        $pid = pcntl_fork(); //创建子进程
        if ($pid == 0) {//子进程
            $pid = posix_getpid();
            echo "* Process {$pid} was created, and Executed:\n\n";
            eval($input); //解析命令
            exit;
        } else {//主进程
            $pid = pcntl_wait($status, WUNTRACED); //取得子进程结束状态
            if (pcntl_wifexited($status)) {
                echo "\n\n* Sub process: {$pid} exited with {$status}";
            }
        }
    }
    
    
}