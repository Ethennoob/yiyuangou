<?php
define('__ROOT__', dirname(dirname(__FILE__)));
require __ROOT__.'/System/Entrance.class.php';
\System\Entrance::action();
    // 指定允许其他域名访问  
    header('Access-Control-Allow-Origin:*');  
    // 响应类型  
    header('Access-Control-Allow-Methods:POST');  
    // 响应头设置  
    header('Access-Control-Allow-Headers:x-requested-with,content-type');  

