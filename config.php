<?php

/*
 *  全局网站配置文件
 */


return [

    'IS_DB_DEBUG' => true,
    // 数据库(主库)(必须存在，请勿删除)
     "DB_MASTER" => array(
      "host" => "203.195.151.134",
      'user' => 'oneBuy',
      "password" => "hanzikeji_oneBuy1124",
      "dbName" => "oneBuy"
      ),

    //测试
    "DB_MASTER" => array(
     "host" => "203.195.151.134",
      'user' => 'oneBuy',
      "password" => "hanzikeji_oneBuy1124",
      "dbName" => "oneBuy"
    ),
    // 只读数据库
    "DB_READ" => array(
      "host" => "203.195.151.134",
      'user' => 'oneBuy',
      "password" => "hanzikeji_oneBuy1124",
      "dbName" => "oneBuy"
    ),
  // 旧数据库
  "DB_GROUPBUY" => array(
      "host" => "localhost",
      'user' => 'root',
      "password" => "root",
      "dbName" => "fat",
  ),

    // memcached配置
    /* 'MEMCACHE_CONFIG' => array(
      'host' => array(
      'host' => '',
      'port' => '11212'
      ),
      array(
      'host' => '',
      'port' => '11213'
      )
      ), */

    //memcached配置
    'MEMCACHE_CONFIG' => array(
        array(
            'host' => '127.0.0.1',
            'port' => '11211'
        ),
    ),
    //redis配置
    'REDIS_CONFIG' => array(
        'host' => '',
        'port' => '',
        'auth' => '',
        'connectType' => 'connect'    //pconnect && connect
    ),
    /*  // 微信配置
      'WEIXIN_CONFIG' => array(
      'token' => '61e303b6638e23dafc4713c43002b4b4', // 填写你设定的key
      'encodingaeskey' => '', // 填写加密用的EncodingAESKey
      'appid' => 'wxfd18311ddcb8de73', // 填写高级调用功能的app id
      'appsecret' => '8841c34efe9b69f56e3fb12f681b9017'
      ), // 填写高级调用功能的密钥 */

    // 测试
    'WEIXIN_CONFIG' => array(
        'token' => 'OneTrade', // 填写你设定的key
        'encodingaeskey' => 'UbobcIF5nFgcqwXAFoytFa6EufA5kyB0vFzIPDThHR4', // 填写加密用的EncodingAESKey
        'appid' => 'wxe50a5528b729b1aa', // 填写高级调用功能的app id
        'appsecret' => '5db67c37c90211bf57d07af54455a02a',
    ), // 填写高级调用功能的密钥
    //微信支付配置
    /*'WEIXIN_PAY_CONFIG' => array(
        'appid' => 'wx500d53069de27d71',
        'mchid' => '1282518701', //商户号
        'key' => 'SKINTALKyangmaimai20151212linlin', //key
        //证书路径,注意应该填写绝对路径
         'SSLCERT_PATH' => '/disk2/ftp/buy/www/cert/apiclient_cert.pem',
         'SSLKEY_PATH' => '/disk2/ftp/buy/www/cert/apiclient_key.pem',
        //异步通知url，商户根据实际开发过程设定
        'NOTIFY_URL' => gethost() . '/Api/Callback/order',
        //curl超时设置
        'CURL_TIMEOUT' => 30,
    ),*/
    //支付宝配置参数
    'ALIPAY_CONFIG'=>array(
       'partner' =>'20********50',   //这里是你在成功申请支付宝接口后获取到的PID；
       'key'=>'9t***********ie',//这里是你在成功申请支付宝接口后获取到的Key
       'sign_type'=>strtoupper('MD5'),
       'input_charset'=> strtolower('utf-8'),
       'cacert'=> getcwd().'\\cacert.pem',
       'transport'=> 'http',
       ),
       //以上配置项，是从接口包中alipay.config.php 文件中复制过来，进行配置；

    'ALIPAY'   =>array(
      //付款账户名
      'account_name'=>'支付宝名字名字名字',
      //付款账号
      'email'=>'pay@xxx.com',
      //这里是异步通知页面url，提交到项目的Pay控制器的notifyurl方法；
      'notify_url'=>'http://www.xxx.com/Pay/notifyurl',
      //这里是页面跳转通知url，提交到项目的Pay控制器的returnurl方法；
      'return_url'=>'http://www.xxx.com/Pay/returnurl',
      //支付成功跳转到的页面，我这里跳转到项目的User控制器，myorder方法，并传参payed（已支付列表）
      'successpage'=>'User/myorder?ordtype=payed',
      //支付失败跳转到的页面，我这里跳转到项目的User控制器，myorder方法，并传参unpay（未支付列表）
      'errorpage'=>'User/myorder?ordtype=unpay',
      ),
    'MSM_URL' => 'http://211.147.239.62:9050/cgi-bin/sendsms?',
    'MSM_ACCOUNT' => 'AA@GZAA',
    'MSM_PWD' => 'Tea9936@',

];
