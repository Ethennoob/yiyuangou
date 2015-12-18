<?php
/**
 * 设置session的过期时间，并启动session，并且检查session是否已经过期。
 * @param boolean $byAjaxWS 是否由AJAX的服务端程序调用，由此得到不同的对会话过期的处理方式。
 */
function startSession() {
	session_name('HANZIGLOBAL');

	//使用memcache存储session(并发大的时候请使用该方法)
	/* $mem=new \System\MyMemCached();
	$session=new \System\Session($mem); */

	//使用本地硬盘
	session_start();	
}

function logError($err) {
    file_put_contents($_SERVER["DOCUMENT_ROOT"] . "/Log/error_log.txt", date("Y-m-d H:i:s") . "-----------" . $err . PHP_EOL, FILE_APPEND);
}

/**
 * POST 请求
 * @param string $url
 * @param array $param
 * @return string content
 */
function httpPost($url, $param,$post_file=false) {
    $oCurl = curl_init();
    if (stripos($url, "https://") !== FALSE) {
        curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
    }
    if (is_string($param) || $post_file) {
        $strPOST = $param;
    } else {
        $aPOST = array();
        foreach ($param as $key => $val) {
            $aPOST[] = $key . "=" . urlencode($val);
        }
        $strPOST = join("&", $aPOST);
    }
    curl_setopt($oCurl, CURLOPT_URL, $url);
    curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($oCurl, CURLOPT_POST, true);
    curl_setopt($oCurl, CURLOPT_POSTFIELDS, $strPOST);
    $sContent = curl_exec($oCurl);
    $aStatus = curl_getinfo($oCurl);
    curl_close($oCurl);
    if (intval($aStatus["http_code"]) == 200) {
        return $sContent;
    } else {
        return false;
    }
}

function httpPostJson($url, $json) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json; charset=utf-8',
        'Content-Length: ' . strlen($json))
    );
    ob_start();
    curl_exec($ch);
    $return_content = ob_get_contents();
    ob_end_clean();

    $return_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if ($return_code == 200) {
        return $return_content;
    } else {
        return false;
    }
}

/**
 * GET 请求
 * @param string $url
 */
function httpGet($url) {
    $oCurl = curl_init();
    if (stripos($url, "https://") !== FALSE) {
        curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, FALSE);
    }
    curl_setopt($oCurl, CURLOPT_URL, $url);
    curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
    $sContent = curl_exec($oCurl);
    $aStatus = curl_getinfo($oCurl);
    curl_close($oCurl);
    if (intval($aStatus["http_code"]) == 200) {
        return $sContent;
    } else {
        return false;
    }
}

/**
 * 浏览器友好的变量输出
 * @param mixed $var 变量
 * @param boolean $echo 是否输出 默认为True 如果为false 则返回输出字符串
 * @param string $label 标签 默认为空
 * @param boolean $strict 是否严谨 默认为true
 * @return void|string
 */
function dump($var, $echo = true, $label = null, $strict = true) {
    $label = ($label === null) ? '' : rtrim($label) . ' ';
    if (!$strict) {
        if (ini_get('html_errors')) {
            $output = print_r($var, true);
            $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
        } else {
            $output = $label . print_r($var, true);
        }
    } else {
        ob_start();
        var_dump($var);
        $output = ob_get_clean();
        if (!extension_loaded('xdebug')) {
            $output = preg_replace('/\]\=\>\n(\s+)/m', '] => ', $output);
            $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
        }
    }
    if ($echo) {
        echo($output);
        return null;
    } else
        return $output;
}

/**
 * URL重定向
 * @param string $url 重定向的URL地址
 * @param integer $time 重定向的等待时间（秒）
 * @param string $msg 重定向前的提示信息
 * @return void
 */
function redirect($url, $time = 0, $msg = '') {
    //多行URL地址支持
    $url = str_replace(array("\n", "\r"), '', $url);
    if (empty($msg))
        $msg = "系统将在{$time}秒之后自动跳转到{$url}！";
    if (!headers_sent()) {
        // redirect
        if (0 === $time) {
            header('Location: ' . $url);
        } else {
            header("refresh:{$time};url={$url}");
            echo($msg);
        }
        exit();
    } else {
        $str = "<meta http-equiv='Refresh' content='{$time};URL={$url}'>";
        if ($time != 0)
            $str .= $msg;
        exit($str);
    }
}

/**
 * 根据PHP各种类型变量生成唯一标识号
 * @param mixed $mix 变量
 * @return string
 */
function toGuidString($mix) {
    if (is_object($mix)) {
        return spl_object_hash($mix);
    } elseif (is_resource($mix)) {
        $mix = get_resource_type($mix) . strval($mix);
    } else {
        $mix = serialize($mix);
    }
    return md5($mix);
}

/**
 * XML编码
 * @param mixed $data 数据
 * @param string $root 根节点名
 * @param string $item 数字索引的子节点名
 * @param string $attr 根节点属性
 * @param string $id   数字索引子节点key转换的属性名
 * @param string $encoding 数据编码
 * @return string
 */
function xmlEncode($data, $root = 'think', $item = 'item', $attr = '', $id = 'id', $encoding = 'utf-8') {
    if (is_array($attr)) {
        $_attr = array();
        foreach ($attr as $key => $value) {
            $_attr[] = "{$key}=\"{$value}\"";
        }
        $attr = implode(' ', $_attr);
    }
    $attr = trim($attr);
    $attr = empty($attr) ? '' : " {$attr}";
    $xml = "<?xml version=\"1.0\" encoding=\"{$encoding}\"?>";
    $xml .= "<{$root}{$attr}>";
    $xml .= dataToXml($data, $item, $id);
    $xml .= "</{$root}>";
    return $xml;
}

/**
 * 数据XML编码
 * @param mixed  $data 数据
 * @param string $item 数字索引时的节点名称
 * @param string $id   数字索引key转换为的属性名
 * @return string
 */
function dataToXml($data, $item = 'item', $id = 'id') {
    $xml = $attr = '';
    foreach ($data as $key => $val) {
        if (is_numeric($key)) {
            $id && $attr = " {$id}=\"{$key}\"";
            $key = $item;
        }
        $xml .= "<{$key}{$attr}>";
        $xml .= (is_array($val) || is_object($val)) ? dataToXml($val, $item, $id) : $val;
        $xml .= "</{$key}>";
    }
    return $xml;
}

/**
 * 获取客户端IP地址
 * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
 * @param boolean $adv 是否进行高级模式获取（有可能被伪装）
 * @return mixed
 */
function getClientIp($type = 0, $adv = false) {
    $type = $type ? 1 : 0;
    static $ip = NULL;
    if ($ip !== NULL)
        return $ip[$type];
    if ($adv) {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $pos = array_search('unknown', $arr);
            if (false !== $pos)
                unset($arr[$pos]);
            $ip = trim($arr[0]);
        }elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
    } elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    // IP地址合法验证
    $long = sprintf("%u", ip2long($ip));
    $ip = $long ? array($ip, $long) : array('0.0.0.0', 0);
    return $ip[$type];
}

/**
 *  获取主机
 */
function getHost() {
    @$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    @$url = $protocol . $_SERVER['HTTP_HOST'];
    return $url;
}

/**
 *  获取当前完整网址url
 */
function getHostUrl() {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $url = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    return $url;
}

/**
 * Ajax方式返回数据到客户端
 * @access protected
 * @param mixed $data 要返回的数据
 * @param String $type AJAX返回数据格式
 * @param int $json_option 传递给json_encode的option参数
 * @return void
 */
function ajaxReturn($data, $type = '', $json_option = 0) {
    if (empty($type))
        $type = 'JSON';
    switch (strtoupper($type)) {
        case 'JSON' :
            // 返回JSON数据格式到客户端 包含状态信息
            header('Content-Type:application/json; charset=utf-8');
            exit(json_encode($data, $json_option));
        case 'XML' :
            // 返回xml格式数据
            header('Content-Type:text/xml; charset=utf-8');
            exit(xmlEncode($data));
        case 'EVAL' :
            // 返回可执行的js脚本
            header('Content-Type:text/html; charset=utf-8');
            exit($data);
        default :
            // 用于扩展其他返回格式数据
            exit();
    }
}

/**
 * 判断目录是否存在
 * @param str $path 目录
 * @return bool
 */
function dirExists($path) {
    $f = true;
    if (file_exists($path) == false) {//创建图片目录
        if (mkdir($path, 0777, true) == false)
            $f = false;
        else if (chmod($path, 0777) == false)
            $f = false;
    }

    return $f;
}

/**
 * 判断数组是否空值(可为0)
 * @param $atr 需要判断的数组
 * @return array,$this->atrErrorKey
 */
function isNotEmpty($atr) {
    foreach ($atr as $k => $v) {
    	if(!is_array($v)){
    		if (empty($v)) {
    			//$this->atrErrorKey=$key;
    			//error_log($k);
    			return false;
    		}
    	}
    }

    return true;
}

/**
 * 生成guid
 * */
function createGuid() {
    $charid = strtoupper(md5(uniqid(mt_rand(), true)));
    $hyphen = chr(45); // "-"
    //chr(123)// "{"
    $uuid =  substr($charid, 0, 8) . $hyphen
    . substr($charid, 8, 4) . $hyphen
    . substr($charid, 12, 4) . $hyphen
    . substr($charid, 16, 4) . $hyphen
    . substr($charid, 20, 12);
    //. chr(125); // "}"
    return $uuid;
}
/*
 * 保留2位小数，不要四舍五入
 */
function cutTwoDecimal($k) {
    $arr = explode(".", $k);
    $a = substr($arr[1], 0, 2);
    $number = empty($a) ? '00' : $a;
    $result = $arr[0] . '.' . $number;
    return $result;
}
/**
 * 生成新浪ID
 * @return string
 */
function createID() {
    $guid = md5(time() + uniqid());
    $uniqid = uniqid($guid, false);
    $uniqidM = md5(substr($uniqid, 39, 9));
    return '826' . substr($uniqid, 39, 9) . substr($uniqidM, 0, 8);
}

/*
 * 生成订单号
 */
function getOrderNumber() {
    /* $guid= md5(time()+uniqid());
      $uniqid=uniqid($guid,false);
      mt_srand((double) microtime() * 1000000);
      return date('ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT).substr($uniqid, 39, 9).mt_rand(50001,90001); */
    $mtime = microtime() * 1000000;
    $time = time();
    $no1 = md5($mtime . $time);
    $no2 = substr($no1, 0, 4);
    $no3 = substr(md5(uniqid(mt_rand(), true)), 0, 12);
    return date('ymd') . $no2 . $no3;
}

/*
 * 获取下个月的当天时间，如果没有当天则返回下个月最后一天的时间格式
 * $date：传入时间戳
 * @return :字符串格式 ‘2015-12-25’或者时间戳
 */
function getNextMonthDay($timestamp, $isReturnUXTime = false) {
    $strDate = null;
    //$timestamp = strtotime($date);
    $arr = getdate($timestamp);
    $theDay = $arr['mday'];
    if ($arr['mon'] == 12) {
        $year = $arr['year'] + 1;
        $month = $arr['mon'] - 11;
        $firstday = $year . '-0' . $month . '-01';
        $lastday = date('Y-m-d', strtotime("$firstday +1 month -1 day"));
    } else {
        $firstday = date('Y-m-01', strtotime(date('Y', $timestamp) . '-' . (date('m', $timestamp) + 1) . '-01'));
        $lastday = date('Y-m-d', strtotime("$firstday +1 month -1 day"));
    }
    $nextMonArr = getdate(strtotime($lastday));
    $nextMonLastDay = $nextMonArr['mday'];
    $theYear = $nextMonArr['year'];
    $theMonth = $nextMonArr['mon'];
    if ($nextMonLastDay < $theDay) {
        $strDate = $lastday;
    } else {
        $strDate = $theYear . '-' . $theMonth . '-' . $theDay;
    }
    $returnTime = $isReturnUXTime ? strtotime($strDate) : $strDate;
    return $returnTime;
}
/*
 * N个月之后的当天时间
 * $timestamp 传入时间戳
 * $addMonth 月的个数
 * $isReturnUXTime 是否转换成时间戳返回，默认否
 * @return 返回N个月时候的当天时间
 */
function getMonthDay($timestamp, $addMonth,$isReturnUXTime = false) {
    $strDate = null;
    //$timestamp = strtotime($date);
    $arr = getdate($timestamp);
    $theDay = $arr['mday'];
    $sumMonth = $arr['mon'] + $addMonth; //累加的月份
    if ($sumMonth > 12) {
        $year = $arr['year'] + intval($sumMonth / 12);
        $month = $sumMonth % 12;
        //$paddZero = $month < 10 ? '-0' : '-';
        $firstday = $year . '-' . $month . '-01'; //echo $firstday.'---'.$month;exit;
        $lastday = date('Y-m-d', strtotime("$firstday +1 month -1 day"));
    } else {
        $firstday = date('Y-m-01', strtotime(date('Y', $timestamp) . '-' . (date('m', $timestamp) + 1) . '-01'));
        $lastday = date('Y-m-d', strtotime("$firstday +1 month -1 day"));
    }
    $targetMonArr = getdate(strtotime($lastday));
    $targetMonLastDay = $targetMonArr['mday'];
    $theYear = $targetMonArr['year'];
    $theMonth = $targetMonArr['mon'];
    if ($targetMonLastDay < $theDay) {
        $strDate = $lastday;
    } else {
        $strDate = $theYear . '-' . $theMonth . '-' . $theDay;
    }
    $returnTime = $isReturnUXTime ? strtotime($strDate) : $strDate;
    return $returnTime;
}

/**
 * 开启sql debug时纪录数据库操作
 * @param string $sql
 * @param string $error
 */
function sqlDebugLog($sql,$error=''){
    $fileName=__ROOT__."/Log/database/sql_".date("Ymd").'.txt';
    
    if (dirExists(dirname($fileName))){
        file_put_contents($fileName, date("Y/m/d H:i:s") . '----' .PHP_EOL. $sql .PHP_EOL.$error.PHP_EOL, FILE_APPEND);
    }
}