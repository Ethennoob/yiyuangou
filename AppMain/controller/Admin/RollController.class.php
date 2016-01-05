<?php
/**
 * 一元购系统---抽奖类
 * @authors 凌翔 (553299576@qq.com)
 * @date    2015-11-28 16:23:10
 * @version $Id$
 */

namespace AppMain\controller\Admin;
use \System\BaseClass;

class RollController extends BaseClass {
    
    /**
     * 抽奖
     */
    public function roll(){
    	$this->V(['goods_id'=>['egNum']]);
        $goods_id = intval($_POST['goods_id']);
        //查询商品是否删除
        $good = $this->table('goods')->where(['is_on'=>1,'id'=>$goods_id])->get(['id','price','thematic_id','goods_sn'],true);
        if (!$good) {
        	$this->R('',70009);
        }
        //查询是否符合开奖要求(认购码全部卖完)
        $code = $this->table('code')->where(['is_on'=>1,'is_use'=>0,'goods_id'=>$goods_id])->get(['code'],false);
        if ($code) {
        	$this->R('',90002);
        }
        //抽奖(得到抽奖key,去匹配code表内的数据)
        $luckyKey = rand(1,$good['price']);
        $code = $this->table('code')->where(['is_on'=>1,'is_use'=>1,'goods_id'=>$goods_id,'key'=>$luckyKey])->update(['is_lucky'=>1,'update_time'=>time()]);
        if (!$code) {
        	$this->R('',40001);
        }
        $luckyCode = $this->table('code')->where(['is_on'=>1,'is_use'=>1,'goods_id'=>$goods_id,'is_lucky'=>1])->get(['code','user_id'],true);
        if (!$luckyCode) {
        	$this->R('',70009);
        }
        $add_time = $this->table('purchase')->where(['is_on'=>1,'goods_id'=>$goods_id,'code'=>$luckyCode['code']])->get(['add_time'],true);
        if (!$add_time) {
            $this->R('',70009);
        }
        $record_id = $this->table('record')->where(['is_on'=>1,'goods_id'=>$goods_id,'add_time'=>$add_time['add_time']])->get(['id'],true);
        if (!$record_id) {
            $this->R('',70009);
        }
        $data = array(
            'goods_id' => $goods_id,
            'thematic_id' =>$good['thematic_id'],
            'bill_sn' =>$good['goods_sn'],
            'user_id' => $luckyCode['user_id'],
            'record_id' => $record_id['id'],
            'code' => $luckyCode['code'],
            'add_time' => time(),
        );
        //生成订单
        $order = $this->table('bill')->save($data);
        if (!$order) {
            $this->R('',40001);
        }
        //code表中该商品code全部is_on=0
        $code = $this->table('code')->where(['is_on'=>1,'is_use'=>1,'goods_id'=>$goods_id])->update(['is_on'=>0,'update_time'=>time()]);
        if (!$code) {
            $this->R('',40001);
        }
        $this->R();
    }
}