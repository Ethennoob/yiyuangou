<?php
/**
 * 反向团购系统---首页商品类
 * @authors 凌翔 (553299576@qq.com)
 * @date    2016-01-08 13:50:57
 * @version $Id$
 */

namespace AppMain\controller\Groupbuy;
use \System\BaseClass;

class GoodsController extends BaseClass {
    /**
     * 首页
     * 商品主图，开团人数，商品名称，商品标题，售价，市场价
     */
    public function index(){
    $pageInfo = $this->P();
    $file = ['id','goods_name','goods_title','goods_album','num','price','market_price'];

    $class = $this->table('groupbuy_goods')->where(['is_on'=>1])->order('add_time desc');

    //查询并分页
    $index = $this->getOnePageData($pageInfo,$class,'get','getListLength',[$file],false);
    if($index){
        foreach ($index  as $k=>$v){
        	$ImgUrl=explode(';', $v['goods_album']);
            $index[$k]['goods_img'] = $ImgUrl[0];
            unset($index[$k]['goods_album'] );
        }      	
    }else{
        $index  = null;
    }
    //返回数据，参见System/BaseClass.class.php方法
    $this->R(['index'=>$index,'pageInfo'=>$pageInfo]);
    }
    /**
     * 商品详情
     */
    public function goodsOneDetail(){
    	$this->V(['goods_id'=>['egNum']]);
        $id = intval($_POST['goods_id']);
        $goods = $this->table('groupbuy_goods')->where(['id'=>$id,'is_on'=>1])->get(null,true);
        if(!$goods){
            $this->R('',70009);
        }
        $record = $this->table('record')->where(['goods_id'=>$id,'is_on'=>1])->get(['id'],false);
        $goods['sale_num'] = count($record);
        $albumList = explode(";",$goods['goods_album'] );
        unset($albumList[count($albumList)-1]);
        $goods['img'] = $albumList;
        unset($goods['goods_album']);
        $goods['add_time'] = date('Y-m-d H:i:s',$goods['add_time']);
        $goods['update_time'] = date('Y-m-d H:i:s',$goods['update_time']);

        $this->R(['goods'=>$goods]);
    }
}