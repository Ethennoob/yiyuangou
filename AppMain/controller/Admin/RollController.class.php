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
     * 将要抽奖的goods_id存入roll中
     */
    public function saveRollGoodID(){
        $this->V(['goods_id'=>['egNum']]);
        $data['goods_id'] = intval($_POST['goods_id']);
        $good = $this->table('roll')->where(['is_on'=>1,'goods_id'=>$data['goods_id']])->get(['id'],true);
        if ($good) {
            $this->R('',40001);
        }
        @$this->table('roll')->save($data);
        $this->R();
    }
    /**
     * 抽奖
     */
    public function roll(){
    	$this->V(['B'=>['egNum']]);
        $data['Bvalue'] = intval($_POST['B']);
        $B = intval($_POST['B']);
        $this->V(['ssc'=>['egNum']]);
        $shishicai = intval($_POST['ssc']);

        $Bvalue = $this->table('system')->where(['id'=>1])->update($data);
        if (!$Bvalue) {
            $this->R('',40001);
        }
        ///////从roll表中查现在要开奖的商品///////
        $goods_id = $this->table('roll')->where(['is_on'=>1])->get(['goods_id'],false);
        if (!$goods_id) {
            $this->R('',99999);
        }
        foreach ($goods_id as $key => $v) {
        //查询商品是否删除
        $good = $this->table('goods')->where(['is_on'=>1,'id'=>$v['goods_id']])->get(['id','company_id','price','thematic_id','goods_sn'],true);
        if (!$good) {
        	$this->R('',70009);
        }
        //查询是否符合开奖要求(认购码全部卖完)
        $code = $this->table('code')->where(['is_on'=>1,'is_use'=>0,'goods_id'=>$v['goods_id']])->get(['code'],false);
        if ($code) {
        	$this->R('',90002);
        } 
        //抽奖(得到抽奖key,去匹配code表内的数据)
        $time = $this->table('record')->where(['is_on'=>1])->limit(50)->order('add_time desc')->get(['user_id','add_time','ms_time'],false);
        if (!$time) {
            $this->R('',70009);
        }
        foreach ($time as $key => $va) {
            $date = date('H:i:s',$va['add_time']);
            $dateArr = explode(':', $date);
            $temp[] = implode('',$dateArr).$va['ms_time'];
        $data = array(
            'goods_id' => $v['goods_id'],
            'user_id' => $va['user_id'],
            'time' => $va['add_time'],
            'shishicai' => $shishicai,
            'B' => $B,
            'ms_time' => $va['ms_time'],
        );
        //生成roll_record
        $roll_record = $this->table('roll_record')->save($data);
        if (!$roll_record) {
            $this->R('',40001);
        }
        }
        $Avalue = array_sum($temp);
        $Bvalue = $this->table('system')->where(['id'=>1])->get(['Bvalue'],true);
        $luckyCode = ($Avalue+$Bvalue['Bvalue'])%$good['price']+10000001;
        ///////////抽奖完/////////
        $code = $this->table('code')->where(['is_on'=>1,'is_use'=>1,'goods_id'=>$v['goods_id'],'code'=>$luckyCode])->update(['is_lucky'=>1,'update_time'=>time()]);
        if (!$code) {
        	$this->R('',40001);
        }

        $luckyCode = $this->table('code')->where(['is_on'=>1,'is_use'=>1,'goods_id'=>$v['goods_id'],'is_lucky'=>1])->get(['user_id','code'],true);
        if (!$luckyCode) {
        	$this->R('',70009);
        }
        $add_time = $this->table('purchase')->where(['is_on'=>1,'goods_id'=>$v['goods_id'],'code'=>$luckyCode['code']])->get(['record_id'],true);
        if (!$add_time) {
            $this->R('',70009);
        }

        $record_id = $this->table('record')->where(['is_on'=>1,'goods_id'=>$v['goods_id'],'id'=>$add_time['record_id']])->get(['id'],true);
        if (!$record_id) {
            $this->R('',70009);
        }
        $address_id = $this->table('user_address')->where(['is_on'=>1,'is_default'=>1,'user_id'=>$luckyCode['user_id'],])->get(['id'],true);
        if (!$record_id) {
            $this->R('',70009);
        }
        $data = array(
            'goods_id' => $v['goods_id'],
            'thematic_id' =>$good['thematic_id'],
            'bill_sn' =>$good['goods_sn'],
            'user_id' => $luckyCode['user_id'],
            'record_id' => $record_id['id'],
            'address_id' => $address_id['id'],
            'company_id' =>$good['company_id'],
            'code' => $luckyCode['code'],
            'add_time' => time(),
        );
        //生成订单
        $order = $this->table('bill')->save($data);
        if (!$order) {
            $this->R('',40001);
        }
        //code表中该商品code全部is_on=0
        $code = $this->table('code')->where(['is_on'=>1,'is_use'=>1,'goods_id'=>$v['goods_id']])->update(['is_on'=>0,'update_time'=>time()]);
        if (!$code) {
            $this->R('',40001);
        }
        }
        $address = $this->table('roll')->where(['is_on'=>1])->delete();
        if(!$address){
            $this->R('',40001);
        }
        $this->R();
    }

    /**
     * 晚上抽奖(2:00---10:00)
     */
    public function nightRoll(){
        $this->V(['goods_id'=>['egNum']]);
        $goods_id = intval($_POST['goods_id']);
        //查询商品是否删除
        $good = $this->table('goods')->where(['is_on'=>1,'id'=>$goods_id])->get(['id','company_id','price','thematic_id','goods_sn'],true);
        if (!$good) {
            $this->R('',70009);
        }

        //查询是否符合开奖要求(认购码全部卖完)
        $code = $this->table('code')->where(['is_on'=>1,'is_use'=>0,'goods_id'=>$goods_id])->get(['code'],false);
        if ($code) {
            $this->R('',90002);
        }
        //计算B值
        $goods_purchase = $this->table('record')->where(['is_on'=>1,'goods_id'=>$goods_id])->get(['user_id'],false);
        if (!$goods_purchase) {
            $this->R('',70009);
        }
        foreach ($goods_purchase as $key => $value) {
            $aa[]=$value['user_id'];
        }
        $nightBValue = sprintf("%05d",count(array_count_values ($aa)));
        $shishicai=0;
        //抽奖(得到抽奖key,去匹配code表内的数据)
        $time = $this->table('record')->where(['is_on'=>1])->limit(50)->order('add_time desc')->get(['user_id','add_time','ms_time'],false);
        if (!$time) {
            $this->R('',70009);
        }
        foreach ($time as $key => $v) {
            $date = date('H:m:s',$v['add_time']);
            $dateArr = explode(':', $date);
            $temp[] = implode('',$dateArr).$v['ms_time'];
            $data = array(
            'goods_id' => $goods_id,
            'user_id' => $v['user_id'],
            'time' => $v['add_time'],
            'shishicai' => $shishicai,
            'B' => $nightBValue,
            'ms_time' => $v['ms_time'],
        );
        //生成roll_record
        $roll_record = $this->table('roll_record')->save($data);
        if (!$roll_record) {
            $this->R('',40001);
        }
        }
        $Avalue = array_sum($temp);
        //$Bvalue = $this->table('system')->where(['id'=>1])->get(['Bvalue'],true);
        $luckyCodeA = ($Avalue+$nightBValue)%$good['price']+10000001;
        ///////////抽奖完/////////
        $code = $this->table('code')->where(['is_on'=>1,'is_use'=>1,'goods_id'=>$goods_id,'code'=>$luckyCodeA])->update(['is_lucky'=>1,'update_time'=>time()]);
        if (!$code) {
            $this->R('',40001);
        }
        $luckyCode = $this->table('code')->where(['is_on'=>1,'is_use'=>1,'goods_id'=>$goods_id,'is_lucky'=>1])->get(['user_id','code'],true);
        if (!$luckyCode) {
            $this->R('',70009);
        }
        $add_time = $this->table('purchase')->where(['is_on'=>1,'goods_id'=>$goods_id,'code'=>$luckyCode['code']])->get(['record_id'],true);
        if (!$add_time) {
            $this->R('',70009);
        }
        $record_id = $this->table('record')->where(['is_on'=>1,'goods_id'=>$goods_id,'id'=>$add_time['record_id']])->get(['id'],true);
        if (!$record_id) {
            $this->R('',70009);
        }

        $address_id = $this->table('user_address')->where(['is_on'=>1,'is_default'=>1,'user_id'=>$luckyCode['user_id'],])->get(['id'],true);
        if (!$record_id) {
            $this->R('',70009);
        }
        $data = array(
            'goods_id' => $goods_id,
            'thematic_id' =>$good['thematic_id'],
            'bill_sn' =>$good['goods_sn'],
            'user_id' => $luckyCode['user_id'],
            'record_id' => $record_id['id'],
            'address_id' => $address_id['id'],
            'company_id' =>$good['company_id'],
            'code' => $luckyCodeA,
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
    /**
     * 查询此时需要抽奖的商品
     */
    public function rollGoodsList(){

        $pageInfo = $this->P();
        $file = ['id','goods_id'];

        $class = $this->table('roll')->where(['is_on'=>1])->order('id desc');

        //查询并分页
        $rollGoodsList = $this->getOnePageData($pageInfo,$class,'get','getListLength',[$file],false);
        if($rollGoodsList ){
            foreach ($rollGoodsList  as $k=>$v){
                $goodsstatus = $this->table('goods')->where(['is_on'=>1,'id'=>$v['goods_id']])->get(['goods_name','thematic_id','company_id'],true);
                $rollGoodsList[$k]['goods_name'] = $goodsstatus['goods_name'];
                $status = $this->table('thematic')->where(['is_on'=>1,'id'=>$goodsstatus['thematic_id']])->get(['thematic_name'],true);
                $rollGoodsList[$k]['thematic_name'] = $status['thematic_name'];
                $status = $this->table('company')->where(['is_on'=>1,'id'=>$goodsstatus['company_id']])->get(['company_name'],true);
                $rollGoodsList[$k]['company_name'] = $status['company_name'];
            }
        }else{
            $rollGoodsList  = null;
        }
        //返回数据，参见System/BaseClass.class.php方法
        $this->R(['rollGoodsList'=>$rollGoodsList,'pageInfo'=>$pageInfo]);

    }
}