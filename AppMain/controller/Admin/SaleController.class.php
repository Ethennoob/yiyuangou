<?php
/**
 * 一元购系统---销售明细类
 * @authors 凌翔 (553299576@qq.com)
 * @date    2015-11-28 21:09:48
 * @version $Id$
 */

namespace AppMain\controller\Admin;
use \System\BaseClass;

class SaleController extends BaseClass {
    /**
     * 销售记录(分页)     
     * 商品id
     */
    public function oneGoodSaleList(){
        $this->V(['goods_id'=>['egNum',null,true]]);
        $id = intval($_POST['goods_id']);
        $pageInfo = $this->P();
        $file = ['id','user_id','num','add_time'];

        $class = $this->table('record')->where(['is_on'=>1,'goods_id'=>$id])->order('add_time desc');

        //查询并分页
        $detailpage = $this->getOnePageData($pageInfo,$class,'get','getListLength',[$file],false);
        if($detailpage ){
            foreach ($detailpage  as $k=>$v){
                $detailpage [$k]['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
                $status = $this->table('user')->where(['is_on'=>1,'id'=>$v['user_id']])->get(['nickname','user_img','phone'],true);
                $detailpage[$k]['nickname'] = $status['nickname'];
                $detailpage[$k]['user_img'] = $status['user_img'];
                $detailpage[$k]['phone'] = $status['phone'];
               $status = $this->table('purchase')->where(['is_on'=>1,'user_id'=>$v['user_id'],'goods_id'=>$id,'record_id'=>$v['id']])->get(['code'],false);
            //拼接认购码
            $count = count($status);
            for ($i=0; $i < $count; $i++) { 
                $v = implode(",",$status[$i]); //可以用implode将一维数组转换为用逗号连接的字符串
                $temp[] = $v;
            }
            $detailpage[$k]['code']=$temp;
            unset($temp);
            }
        }else{
            $detailpage  = null;
        }
        //返回数据，参见System/BaseClass.class.php方法
        $this->R(['onegoodspage'=>$detailpage,'pageInfo'=>$pageInfo]);
    }
    /**
     * 销售记录(分页)     
     * 商品id
     */
    public function GoodSaleOutputExcel(){

        $this->V(['goods_id'=>['egNum',null,true]]);
        $id = intval($_POST['goods_id']);
        $goods = $this->table('goods')->where(['id'=>$id])->get(['goods_name'],true);
        $data = array(
                'title' =>"商品\"".$goods['goods_name']."\"销售明细表",
                'nickname' => "微信昵称",
                'phone'    =>"手机号码",
                'num'  =>"购买数量/金额",
                'add_time'  =>"购买时间",
                'code'  =>"认购码",
                'remask' =>"备注",
                );
        $file = ['id','user_id','num','add_time'];

        $class = $this->table('record')->where(['is_on'=>1,'goods_id'=>$id])->order('add_time desc')->get($file,false);

        if($class ){
            foreach ($class  as $k=>$v){
                $class [$k]['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
                $status = $this->table('user')->where(['is_on'=>1,'id'=>$v['user_id']])->get(['nickname','user_img','phone'],true);
                $class[$k]['nickname'] = $status['nickname'];
                //$class[$k]['user_img'] = $status['user_img'];
                $class[$k]['phone'] = $status['phone'];
                $class[$k]['remask'] = null;
               $status = $this->table('purchase')->where(['is_on'=>1,'user_id'=>$v['user_id'],'goods_id'=>$id,'record_id'=>$v['id']])->get(['code'],false);
            //拼接认购码
            $count = count($status);
            for ($i=0; $i < $count; $i++) { 
                $v = implode(",",$status[$i]); //可以用implode将一维数组转换为用逗号连接的字符串
                $temp[] = $v;
            }
            $arr = implode(" ", $temp);
                //$temp = $vs;
            //$aa= (string)$temp;

            $class[$k]['code']=$arr;
            //$class[$k]['code']=null;
            unset($temp);
            }
        }else{
            $class  = null;
        }
        $outputExcel = $this->H('Excel')->PHPExcelGood($class,$data);
            if (!$outputExcel) {
                $this->R('',40001);
            }
    }

    /**
     * 商品销售情况(分页)
     * 专题id
     */
    public function goodsSaleRecordList(){
        $this->V(['thematic_id'=>['egNum',null,true]]);
        $id = intval($_POST['thematic_id']);
        $pageInfo = $this->P();
        $file = ['id','goods_name','price','goods_thumb','add_time'];

        $class = $this->table('goods')->where(['is_on'=>1,'thematic_id'=>$id])->order('add_time desc');

        //查询并分页
        $detailpage = $this->getOnePageData($pageInfo,$class,'get','getListLength',[$file],false);
        if($detailpage ){
            foreach ($detailpage  as $k=>$v){
                $detailpage [$k]['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
                $detailpage[$k]['total_num'] = $v['price'];
                $status = $this->table('purchase')->where(['is_on'=>1,'goods_id'=>$v['id']])->get(['id'],false);
                $count = count($status);
                $detailpage[$k]['purchase_num'] = $count;
                $detailpage[$k]['last_num'] =$detailpage[$k]['total_num']-$count; 
            }
        }else{
            $detailpage  = null;
        }
        //返回数据，参见System/BaseClass.class.php方法
        $this->R(['goodspage'=>$detailpage,'pageInfo'=>$pageInfo]);
    }
    /**
     * 中奖情况(分页)
     * 专题id
     */
    public function beforeRecordList(){

    $this->V(['thematic_id'=>['egNum',null,true]]);
    $id = intval($_POST['thematic_id']);
    $pageInfo = $this->P();
    $file = ['id','goods_id','user_id','code','add_time'];

    $class = $this->table('bill')->where(['is_on'=>1,'thematic_id'=>$id])->order('add_time desc');

    //查询并分页
    $before = $this->getOnePageData($pageInfo,$class,'get','getListLength',[$file],false);
    if($before){
        foreach ($before  as $k=>$v){
            $status = $this->table('goods')->where(['is_on'=>1,'id'=>$v['goods_id']])->get(['goods_name','price','goods_thumb'],true);
            $before[$k]['goods_name'] = $status['goods_name'];
            $before[$k]['price'] = $status['price'];
            $before[$k]['goods_thumb'] = $status['goods_thumb'];
            $status = $this->table('user')->where(['is_on'=>1,'id'=>$v['user_id']])->get(['nickname','user_img','phone'],true);
            $before[$k]['nickname'] = $status['nickname'];
            $before[$k]['user_img'] = $status['user_img'];
            $before[$k]['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
        }
    }else{
        $before  = null;
    }
    //返回数据，参见System/BaseClass.class.php方法
    $this->R(['luckypage'=>$before,'pageInfo'=>$pageInfo]);
    }
    
}