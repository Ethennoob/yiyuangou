<?php
/**
 * 一元购系统---前台商品展示类
 * @authors 凌翔 (553299576@qq.com)
 * @date    2015-11-29 16:47:19
 * @version $Id$
 */

namespace AppMain\controller\Api;
use \System\BaseClass;

class GoodsController extends BaseClass {
    public function test(){
        $time = strtotime('2014-06-06 22:06:20');
        $dataClass=$this->H('Roll');
        $billList=$dataClass->rollTime($time);
        var_dump(date('Y-m-d H:i:s',$billList));
        exit();
                    
    }
    /**
     *单个商品展示信息
     *商品id,商品名，商品title，商品desc，上架时间，成本价格，售价，
     *商品原图，商品缩略图，商品相册，是否包邮
     */
    public function purchaseOneDetail(){
    //查询正在进行的专题
        $rule = [
                    'goods_id'   =>['egNum'],
                    'user_id'    =>['egNum'],
                ];
                $this->V($rule); 

                foreach ($rule as $k => $v) {
                        $data[$k] = $_POST[$k];
                }
        $id = intval($_POST['goods_id']);
        $userid = intval($_POST['user_id']);

        //查询一条数据
        $goodinfo = $this->table('goods')->where(['is_on'=>1,'id'=>$id])->get(null,true);
        $albumList = explode(";",$goodinfo['goods_album'] );
        if ($albumList[0]==null) {
            $goodinfo['img'] = null;
        }else{
            $goodinfo['img'] = $albumList;
        }
        $count=count($albumList);
        //unset($goodinfo['img'][$count-1]);
        $thematic = $this->table('thematic')->where(['is_on'=>1,'id'=>$goodinfo['thematic_id']])->get(['thematic_name'],true);
        $goodinfo['thematic_name'] = $thematic['thematic_name'];
        $status = $this->table('purchase')->where(['is_on'=>1,'goods_id'=>$id])->get(['id'],false);
        $count = count($status);
        $goodinfo['total_num'] = $goodinfo['price'];
        $goodinfo['purchase_num'] = $count;
        $goodinfo['last_num'] =$goodinfo['total_num']-$count; 
        $goodinfo['add_time'] = date('Y-m-d H:i:s',$goodinfo['add_time']);
        $goodinfo['upload_date'] = date('Y-m-d H:i:s',$goodinfo['upload_date']);
        unset($goodinfo['goods_album']);
        $record_id = $this->table('bill')->where(['is_on'=>1,'goods_id'=>$id])->get(['record_id'
            ,'code','add_time'],true);
        $goodinfo['luckycode'] = $record_id['code'];
        $status = $this->table('purchase')->where(['is_on'=>1,'goods_id'=>$id])->order('add_time desc')->limit(0,1)->get(['add_time'],true);
                if ($status!=null) {
                    ////究极无敌大判定////
                    $time = $status['add_time'];
                    $dataClass=$this->H('Roll');
                    $goodinfo['lucky_time']=$dataClass->rollTime($time);
                }
        //$goodinfo['lucky_time'] = $status['add_time']+240;
        $status = $this->table('record')->where(['is_on'=>1,'id'=>$record_id['record_id']])->get(['user_id'],true);
        $status = $this->table('user')->where(['is_on'=>1,'id'=>$status['user_id']])->get(['id','user_img','area','nickname'],true);
        $goodinfo['user_img'] = $status['user_img'];
        $goodinfo['area'] = $status['area'];
        $goodinfo['nickname'] = $status['nickname'];
        $status = $this->table('record')->where(['is_on'=>1,'user_id'=>$status['id'],'goods_id'=>$id])->get(['num'],false);
        if ($status!==null) {
            foreach ($status as $key => $v) {
            $temp1[]= $v['num'];
            }
            $goodinfo['buynum'] = array_sum($temp1);
        }else{
            $goodinfo['buynum'] = 0;
        }
        $status = $this->table('purchase')->where(['is_on'=>1,'user_id'=>$userid,'goods_id'=>$id])->get(['code'],false);
            //拼接认购码
            $count = count($status);
            for ($i=0; $i < $count; $i++) { 
                $v = implode(",",$status[$i]); //可以用implode将一维数组转换为用逗号连接的字符串
                @$temp[] = $v;
            }
            $goodinfo['user_code']=@$temp;
            unset($temp);



        $this->R(['goodinfo'=>$goodinfo]);
    }
    /**
     * 首页商品展示
     */
    public function index(){
        //传入专区id
        $this->V(['company_id'=>['egNum']]);
        $company_id = intval($_POST['company_id']); 
        
        $company = $this->table('company')->where(['is_on'=>1,'id'=>$company_id])->get(['id','company_name'],true);      
            if (!$company) {
                $index  = null;
                //返回数据，参见System/BaseClass.class.php方法
                $this->R(['index'=>$index,'thematic'=>null,'poster'=>null,'pageInfo'=>null]);
            }
        $company_name = $company['company_name'];
        //传入专题
        if (isset($_POST['thematic_id'])) {
            $this->V(['thematic_id'=>['egNum',null,true]]);
            $id = intval($_POST['thematic_id']);
            //查询专题
            $thematic = $this->table('thematic')->where(['is_on'=>1,'id'=>$id,'company_id'=>$company_id,'is_show'=>1])->get(['id','thematic_name'],true);
            if (!$thematic) {
                $index  = null;
                //返回数据，参见System/BaseClass.class.php方法
                $this->R(['index'=>$index,'thematic'=>null,'poster'=>null,'pageInfo'=>null]);
            }
        }else{
            //查询专题
            $thematic = $this->table('thematic')->where(['is_on'=>1,'status'=>0,'is_show'=>1,'company_id'=>$company_id])->get(['id','thematic_name'],true);
            if (!$thematic) {
                $this->R(['index'=>null,'thematic'=>null,'poster'=>null,'pageInfo'=>null]);
            }
        }

    $pageInfo = $this->P();
    $file = ['id','goods_name','price','goods_img','goods_thumb','limit_num'];

    $class = $this->table('goods')->where(['is_on'=>1,'thematic_id'=>$thematic['id']])->order('add_time desc');

    //查询并分页
    $index = $this->getOnePageData($pageInfo,$class,'get','getListLength',[$file],false);
    if($index){
        foreach ($index  as $k=>$v){
            
            $status = $this->table('purchase')->where(['is_on'=>1,'goods_id'=>$v['id']])->get(['id'],false);
            $count = count($status);
            $index[$k]['total_num'] = $v['price'];
            $index[$k]['purchase_num'] = $count;
            $index[$k]['last_num'] =$index[$k]['total_num']-$count;
            if ($index[$k]['last_num']==0) {
                $status = $this->table('purchase')->where(['is_on'=>1,'goods_id'=>$v['id']])->order('add_time desc')->limit(0,1)->get(['add_time'],true);
                if ($status!=null) {
                    ////究极无敌大判定////
                    $time = $status['add_time'];
                    $dataClass=$this->H('Roll');
                    $index[$k]['lucky_time']=$dataClass->rollTime($time);
                }
               $status = $this->table('bill')->where(['is_on'=>1,'goods_id'=>$v['id']])->get(['user_id','add_time'],true);
               //$index[$k]['lucky_time'] =$status['add_time']+240;
               if ($status) {
                   $status = $this->table('user')->where(['is_on'=>1,'id'=>$status['user_id']])->get(['nickname'],true);
                   $index [$k]['nickname'] =$status['nickname'];
               }/*else{
                $status = $this->table('purchase')->where(['is_on'=>1,'goods_id'=>$v['id']])->order('add_time desc')->limit(0,1)->get(['add_time'],true);
                if ($status!=null) {
                    $index[$k]['lucky_time'] =$status['add_time']+180;//+3分钟;
                }*/
               //}
            }
        }

    }else{
        $index = null;
        //$this->R(['index'=>null,'thematic'=>null,'poster'=>null,'pageInfo'=>null]);
    }
    $thematic['thematic_name'] = $thematic['thematic_name'];
        $advlist = $this->table('advertisement')->where(['is_on'=>1,'company_id'=>$company_id])->order('sort_order asc')->get(['id','adv_name','adv_img','adv_url','sort_order'],false);
        $count = count($advlist);
        if ($advlist==null) {
            $poster = null;
        }else{
            foreach ($advlist as $key => $value) {
            $poster[] = $value;
        }
    //查询最新一期专题
            $newthematic = $this->table('thematic')->where(['is_on'=>1,'company_id'=>$company_id])->order('add_time desc')->get(['id'],false);
            $newthematic = $newthematic[0]['id'];
    //返回数据，参见System/BaseClass.class.php方法
    $this->R(['company_name'=>$company_name,'index'=>$index,'thematic'=>$thematic,'newthematic'=>$newthematic,'poster'=>$poster,'pageInfo'=>$pageInfo]);
    }
    }

}