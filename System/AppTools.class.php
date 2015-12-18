<?php
namespace System;
class AppTools {   
    /**
     *  全站发送短信
     */
    public static function sendSms($phone,$content){
        $result=self::xuanWuMsm($phone,$content);
        
        return $result;
    }
    
    /**
     *  玄武科技短信接口
     */
    public static function xuanWuMsm($phone,$content){
        $url=\System\Entrance::config('MSM_URL')."username=".\System\Entrance::config('MSM_ACCOUNT')."&password=";
        $url.=\System\Entrance::config('MSM_PWD')."&to=".$phone."&text=".urlencode(iconv('utf-8', 'gb2312', $content))."&subid=&msgtype=1";
        $send=httpGet($url);
        if ($send===false){
            $retrun=array("result"=>false,"info"=>"接口请求失败！");
            return $retrun;
        }
    
        $result=false;
        $info="";
        switch ($send){
            case "0" :
                $result=true;
                $info="发送成功！";
                break;
            case "-2" :
                $info="发送参数填定不正确！";
                break;
            case "-3" :
                $info="用户载入延迟！";
                break;
            case "-6" :
                $info="密码错误！";
                break;
            case "-7" :
                $info="用户不存在！";
                break;
            case "-11" :
                $info="发送号码数理大于最大发送数量";
                break;
            case "-12" :
                $info="余额不足";
                break;
            case "-99" :
                $info="内部处理错误";
                break;
            default:
                $info="其他错误";
                break;
        }
        return array("result"=>$result,"info"=>$info);
    }
    
    

    
    
    public static function generateMsgAuthCode() {
        $rand_array = range(0, 9);
        shuffle($rand_array); //调用现成的数组随机排列函数
        return implode('', array_slice($rand_array, 0, 6)); //截取前$limit个
    }
    
    /**
     * 获取微信media并保存
     * @param str $mediaID  media_id
     * @param str $path 目录
     * @return bool
     */
    public static function getWechatMedia($mediaID,$savePath=null){
    	if (empty($savePath)){
    		$savePath='order/'.time().'_'.rand(1000,9999).".jpg";
    	}
    	
        $weObj = new \System\lib\Wechat\Wechat(\System\Entrance::config("WEIXIN_CONFIG"));
        $media=$weObj->getMedia($mediaID);
        
        if(!$media) return false;
        
        $temPath='../TempImg/'; //临时保存路径
        if(!dirExists($temPath)) return false;
        
        $fileName=basename($savePath);
        
        /* $fileName=basename($savePath);
        $checkPath=str_replace($fileName, '', $savePath);
        if(!dirExists($checkPath)) return false; */
        
        if(!file_put_contents($temPath.$fileName, $media)) return false;
        
        //生成压缩图片
        $image=new \System\lib\Image\Image($temPath.$fileName);
        $img1=$image->size();
        $return['img1']['path']=$temPath.$fileName;
        $return['img1']['width']=$img1[0];
        $return['img1']['heigh']=$img1[1];
        
        //200*200缩略图
        $img2Path=$temPath.str_replace('.', '_thumb200.', $fileName);
        $image->thumb(200,10000)->save($img2Path);
        $img2=$image->size();
        $return['img2']['path']=$img2Path;
        $return['img2']['width']=$img2[0];
        $return['img2']['heigh']=$img2[1];
        
        //360*360缩略图
        $img3Path=$temPath.str_replace('.', '_thumb360.', $fileName);
        $image->open($temPath.$fileName);
        $image->thumb(360,10000)->save($img3Path);
        $img3=$image->size();
        $return['img3']['path']=$img3Path;
        $return['img3']['width']=$img3[0];
        $return['img3']['heigh']=$img3[1];
        
        //dump($return);
        //上传
        foreach ($return as $key=>$a){
        	$upload=httpPost(\System\Entrance::config('IMG_UPLOAD'), ['file'=>new \CURLFile(realpath($a['path'])),'savePath'=>dirname($savePath).'/','saveName'=>basename($a['path'])],true);
        	//dump($upload);
        	$upload=json_decode($upload,true);
        	if (!($upload&&$upload['result']==true)){
        			return false;
        	}
        	unlink($a['path']);
        	$return[$key]['path']=$upload['path'];
        }
        
        return $return;
    }
    
    public static function sendEmail($to,$subjet,$content){
        ini_set("magic_quotes_runtime",0);
    
        try {
            $mail = new \System\lib\Mail\PHPMailer(true);
            $mail->IsSMTP();
            $mail->CharSet='UTF-8'; //设置邮件的字符编码，这很重要，不然中文乱码
            $mail->SMTPAuth = true; //开启认证
            $mail->Port = 465;
            //$mail->SMTPDebug=true;
            $mail->Host = "smtp.exmail.qq.com";
            $mail->Username = "chenjialiang@han-zi.cn";
            $mail->Password = "linfeng03";
            //$mail->IsSendmail(); //如果没有sendmail组件就注释掉，否则出现“Could not execute: /var/qmail/bin/sendmail ”的错误提示
            $mail->AddReplyTo("chenjialiang@han-zi.cn","淘学网");//回复地址
            $mail->From = "chenjialiang@han-zi.cn";
            $mail->FromName = "淘学网";
            //$to = "262329131@qq.com";
            $mail->AddAddress($to);
            $mail->Subject = $subjet;
            $mail->Body = $content;
            //$mail->AltBody = "To view the message, please use an HTML compatible email viewer!"; //当邮件不支持html时备用显示，可以省略
            $mail->WordWrap = 80; // 设置每行字符串的长度
            $mail->SMTPSecure='ssl';
            //$mail->AddAttachment("f:/test.png"); //可以添加附件
            $mail->IsHTML(true);
            $mail->Send();
            //echo '邮件已发送';
            return ['result'=>true];
        } catch (\System\lib\Mail\phpmailerException $e) {
            return ['result'=>false,'errorMsg'=>$e->errorMessage()];
        }
    }

}
