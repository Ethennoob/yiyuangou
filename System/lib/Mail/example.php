<?php
//发送邮件
ini_set("magic_quotes_runtime",0);


$to='262329131@qq.com';
$subjet='主题';
$content='内容';


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