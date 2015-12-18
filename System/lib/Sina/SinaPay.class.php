<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Sina
 *新浪资金托管类
 * @author Administrator
 */
namespace System\lib\Sina;
class SinaPay {

    /**
     * getSignMsg 计算前面
     *
     * @param array $pay_params
     *        	计算前面数据
     * @param string $sign_type
     *        	签名类型
     * @return string $signMsg 返回密文
     */
    function getSignMsg($pay_params = array(), $sign_type) {
        $params_str = "";
        $signMsg = "";
        $sina_config=  \System\Entrance::config('SINA_FUND_MANAGED');

        foreach ($pay_params as $key => $val) {
            if ($key != "sign" && $key != "sign_type" && $key != "sign_version" && isset($val) && @$val != "") {
                $params_str .= $key . "=" . $val . "&";
            }
        }
        $params_str = substr($params_str, 0, - 1);
        switch (@$sign_type) {
            case 'RSA' :
                //签名私钥
                $private_key=  $sina_config['private_key'];
                $priv_key = file_get_contents($private_key);
                $pkeyid = openssl_pkey_get_private($priv_key);
                openssl_sign($params_str, $signMsg, $pkeyid, OPENSSL_ALGO_SHA1);
                openssl_free_key($pkeyid);
                $signMsg = base64_encode($signMsg);
                break;
            case 'MD5' :
            default :
                $params_str = $params_str . $sina_config['md5_key'];
                $signMsg = strtolower(md5($params_str));
                break;
        }
        return $signMsg;
    }
    
    /**
     * 通过公钥进行rsa加密
     *
     * @param type $name
     *        	Descriptiondata
     *        	$data 需要进行rsa公钥加密的数据 必传
     *        	$pu_key 加密所使用的公钥 必传
     * @return 加密好的密文
     */
    function Rsa_encrypt($data, $public_key) {
        $encrypted = "";
        $cert = file_get_contents($public_key);
        $pu_key = openssl_pkey_get_public($cert); // 这个函数可用来判断公钥是否是可用的
        openssl_public_encrypt($data, $encrypted, $pu_key); // 公钥加密
        $encrypted = base64_encode($encrypted); // 进行编码
        return $encrypted;
    }
    
    /**
     * [createcurl_data 拼接模拟提交数据]
     *
     * @param array $pay_params        	
     * @return string url格式字符串
     */
    function createcurl_data($pay_params = array()) {
        $params_str = "";
        foreach ($pay_params as $key => $val) {
            if (isset($val) && !is_null($val) && @$val != "") {
                $params_str .= "&" . $key . "=" . urlencode(urlencode(trim($val)));
            }
        }
        if ($params_str) {
            $params_str = substr($params_str, 1);
        }
        return $params_str;
    }

    /**
     * checkSignMsg 回调签名验证
     *
     * @param array $pay_params        	
     * @param string $sign_type        	
     * @return boolean
     */
    function checkSignMsg($pay_params = array(), $sign_type) {
        $params_str = "";
        $signMsg = "";
        $return = false;
        $sina_config=  \System\Entrance::config('SINA_FUND_MANAGED');
        foreach ($pay_params as $key => $val) {
            if ($key != "sign" && $key != "sign_type" && $key != "sign_version" && !is_null($val) && @$val != "") {
                $params_str .= "&" . $key . "=" . $val;
            }
        }
        if ($params_str) {
            $params_str = substr($params_str, 1);
        }
        switch (@$sign_type) {
            case 'RSA' :               
                $public_key=$sina_config['public_key'];//加密公钥
                $cert = file_get_contents($public_key);
                $pubkeyid = openssl_pkey_get_public($cert);
                $ok = openssl_verify($params_str, base64_decode($pay_params ['sign']), $cert, OPENSSL_ALGO_SHA1);
                $return = $ok == 1 ? true : false;
                openssl_free_key($pubkeyid);
                break;
            case 'MD5' :
            default :
                $params_str = $params_str . $sina_config['md5_key'];
                $signMsg = strtolower(md5($params_str));
                $return = (@$signMsg == @strtolower($pay_params ['sign'])) ? true : false;
                break;
        }
        return $return;
    }
    
    /**
     * 文件摘要算法
     */
    function md5_file($filename) {
        return md5_file($filename);
    }
    
    /**
     * sftp上传企业资质
     * sftp upload
     * @param $file 上传文件路径
     * @return false 失败   true 成功
     */
    function sftp_upload($file, $filename) {
        $sina_config=  \System\Entrance::config('SINA_FUND_MANAGED');
        $strServer = $sina_config['sftp_address'];
        $strServerPort = $sina_config['sftp_port'];
        $strServerUsername = $sina_config['sftp_username'];
        $strServerprivatekey = $sina_config['sftp_privatekey'];
        $strServerpublickey = $sina_config['sftp_publickey'];
        $resConnection = ssh2_connect($strServer, $strServerPort);
        if (ssh2_auth_pubkey_file($resConnection, $strServerUsername, $strServerpublickey, $strServerprivatekey)) {
            $resSFTP = ssh2_sftp($resConnection);
            file_put_contents("ssh2.sftp://{$resSFTP}/upload/" . $filename, $file);
            if (!copy($file, "ssh2.sftp://{$resSFTP}/upload/$filename")) {
                return false;
            }
        }
        return true;
    }
    
    /**
     * [curlPost 模拟表单提交]
     *
     * @param string $url        	
     * @param string $data        	
     * @return string $data
     */
    function curlPost($url, $data) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

}
