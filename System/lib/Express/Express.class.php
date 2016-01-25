<?php
/**
 * Express.class.php 快递查询类 v1.0
 *
 * @copyright        
 * @license          
 * @lastmodify       2014-08-22
 */
 namespace System\lib\Express;
class Express
{
    /*
     * 网页内容获取方法
    */
    private function getcontent($url)
    {
        if (function_exists("file_get_contents")) {
            $file_contents = file_get_contents($url);
        } else {
            $ch      = curl_init();
            $timeout = 5;
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            $file_contents = curl_exec($ch);
            curl_close($ch);
        }
        return $file_contents;
    }

    /*
     * 获取对应名称和对应传值的方法
    */
    private function expressname($order)
    {
        $name   = json_decode($this->getcontent("http://www.kuaidi100.com/autonumber/auto?num={$order}"), true);
        $result = $name[0]['comCode'];
        if (empty($result)) {
            return false;
        } else {
            return $result;
        }
    }

    /*
     * 返回$data array      快递数组查询失败返回false
     * @param $order        快递的单号
     * $data['ischeck'] ==1 已经签收
     * $data['data']        快递实时查询的状态 array
    */
    public function getorder($order)
    {
        $keywords = $this->expressname($order);
        if (!$keywords) {
            return false;
        } else {
            //$result = $this->getcontent("http://www.kuaidi100.com/query?type={$keywords}&postid={$order}");
            $result = $this->getcontent("http://api.kuaidi100.com/api?id={qMWBOgEq7733}&com={$keywords}&nu={$order}&valicode={}&show={0}&muti={1}&order={desc}");
            $data   = json_decode($result, true);
            return $data;
        }
    }
    /*
     * 返回$result josn      快递数组查询失败返回false
     * @param $order        快递的单号
     * $data['ischeck'] ==1 已经签收
     * $data['data']        快递实时查询的状态 array
    */
    public function getJsona($order)
    {
        $keywords = $this->expressname($order);
        if (!$keywords) {
            return false;
        } else {
            $result = $this->getcontent("http://www.kuaidi100.com/query?type={$keywords}&postid={$order}");
            //$result = $this->getcontent("http://api.kuaidi100.com/api?id={29833628d495d7a5}&com={$keywords}&nu={$order}&valicode={}&show={0}&muti={1}&order={desc}");
            //$data   = json_decode($result, true);
            return $result;
        }
    }
    public function getJson($order,$form="",$to=""){
        $keywords = $this->expressname($order);
        if (!$keywords) {
            return false;
        } else {
            $post_data = array();
            $post_data["schema"] = 'json' ; 
            //callbackurl请参考callback.php实现，key经常会变，请与快递100联系获取最新key
            $post_data["param"] = '{"company":"'.$keywords.'", "number":"'.$order.'",
            "from":"'.$form.'", "to":"'.$to.'", "key":"qMWBOgEq7733",
            "parameters":{"callbackurl":"http://onebuy.ping-qu.com/Api/Address/callbackurl"}}';

            return  $post_data;      //返回提交结果，格式与指定的格式一致（result=true代表成功）
        }
    }
}
?>
