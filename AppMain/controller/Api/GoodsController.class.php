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
    /**
     *单个商品展示信息
     *商品id,商品名，商品title，商品desc，上架时间，成本价格，售价，
     *商品原图，商品缩略图，商品相册，是否包邮
     */
    public function purchaseOneDetail(){
        $this->V(['id'=>['egNum',null,true]]);
        $id = intval($_POST['id']);

        //查询一条数据
        $goodinfo = $this->table('goods')->where(['is_on'=>1,'id'=>$id])->get(null,true);
        $status = $this->table('purchase')->where(['is_on'=>1,'goods_id'=>$id])->get(['id'],false);
        $count = count($status);
        $goodinfo['total_num'] = $goodinfo['price'];
        $goodinfo['purchase_num'] = $count;
        $goodinfo['last_num'] =$goodinfo['total_num']-$count; 
        $goodinfo['add_time'] = date('Y-m-d H:i:s',$goodinfo['add_time']);
        $goodinfo['upload_date'] = date('Y-m-d H:i:s',$goodinfo['upload_date']);
        $this->R(['商品信息'=>$goodinfo]);
    }
    /**
     * 首页商品展示
     */
    public function index(){
    //查询正在进行的专题
    $thematic = $this->table('thematic')->where(['is_on'=>1,'status'=>0])->get(['id','thematic_name'],true);
    if (!$thematic) {
    	$index  = null;
    }

    $pageInfo = $this->P();
    $file = ['id','goods_name','price'];

    $class = $this->table('goods')->where(['is_on'=>1,'thematic_id'=>$thematic['id']])->order('add_time desc');

    //查询并分页
    $index = $this->getOnePageData($pageInfo,$class,'get','getListLength',[$file],false);
    if($index){
        foreach ($index  as $k=>$v){
            
            $status = $this->table('purchase')->where(['is_on'=>1,'goods_id'=>$v['id']])->get(['id'],false);
            $count = count($status);
            $index[$k]['total_num'] = $v['price'];
            $index[$k]['purchase_num'] = $count;
            $index [$k]['last_num'] =$index[$k]['total_num']-$count; 
        }
    }else{
        $index  = null;
    }
    //返回数据，参见System/BaseClass.class.php方法
    $this->R(['首页专题商品'=>$index,'pageInfo'=>$pageInfo]);
    }
    

}