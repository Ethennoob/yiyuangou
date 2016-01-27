<?php
/**
 * 反向团购系统---商品管理类
 * @authors 凌翔 (553299576@qq.com)
 * @date    2016-01-07 11:53:08
 * @version $Id$
 */

namespace AppMain\controller\Groupbuy;
use \System\BaseClass;

class BackendGoodsController extends BaseClass {
    public function goodsImgUpload(){
        $goodsImg = $this->table('groupbuy_goods')->where(['id'=>4,'is_on'=>1])->get(['goods_album'],false);
        foreach ($goodsImg as $key => $img) {
            $ImgUrl = explode(';', $img['goods_album']);
        }
        $count = count($ImgUrl);
        unset($ImgUrl[$count-1]);
        foreach ($ImgUrl as $k => $oneImgUrl) {
            $oneImgName = explode('/', $oneImgUrl);
            if ($_FILES['goods_album']['name'][$k]!==$oneImgName[4]) {
                var_dump($oneImgUrl);
                //@unlink("../html".$goodsImg['goods_img']);
            }
        }

        exit();


            $a = $_FILES['goods_album']['name'];
            foreach ($a as $v) {
                var_dump($v);
            }
            var_dump($a);
            exit();
        }
    /**
     * 添加商品
     * 名称，标题，开团人数，开团时间，售价，市场价，成本价，库存，相册,是否上架
     */
    public function goodsAdd()
        {
            $rule = [
                    'goods_name'     =>[],
                    'goods_title'    =>[],
                    'num'            =>['egNum'],
                    'group_time'     =>['egNum'],  
                    'price'          =>['money'],
                    'market_price'   =>['money'],
                    'cost_price'     =>['money'],
                    'stock'          =>['egNum'],
                    'is_show'        =>['in',[0,1],true],
                    //'goods_album'  => [],
                ];
                $this->V($rule); 

                foreach ($rule as $k => $v) {
                        $data[$k] = $_POST[$k];
                }

            $data['add_time'] = time();
            $data['discount_rate'] = $data['price']/$data['market_price'];

            //图片上传
            $pictureAlbum = $_FILES;
            $imgarray = $this->H('PictureUpload')->pictureUploadMore($pictureAlbum,'groupbuyAlbum',false);
            $data['goods_album'] = $imgarray['img'];
            $goods = $this->table('groupbuy_goods')->save($data);
            if(!$goods){
                $this->R('',40001);
            }
            $this->R(); 
        }     
	/**
	 * 商品修改
	 * 名称，标题，开团人数，开团时间，售价，市场价，成本价，库存，相册,是否上架
	 */
	public function goodsOneEdit()
        {  
            $rule = [
                    'goods_id'       =>['egNum'],
                    'goods_name'     =>[],
                    'goods_title'    =>[],
                    'num'            =>['egNum'],
                    'group_time'     =>['egNum'],  
                    'price'          =>['money'],
                    'market_price'   =>['money'],
                    'cost_price'     =>['money'],
                    'stock'          =>['egNum'],
                    'is_show'        =>['in',[0,1],true],
                ];
                $this->V($rule); 

                foreach ($rule as $k => $v) {
                        $data[$k] = $_POST[$k];
                }
            $data['update_time'] = time();  
            $data['discount_rate'] = $data['price']/$data['market_price'];       
            $goods = $this->table('groupbuy_goods')->where(['id'=>$data['goods_id'],'is_on'=>1])->get(['id'],true);
            if(!$goods){
                $this->R('',70009);
            }
                $goods = $this->table('groupbuy_goods')->where(['id'=>$data['goods_id'],'is_on'=>1])->update($data);
                if(!$goods){
                    $this->R('',40001);
                }
            $this->R(); 
        }
    /**
     * 查询一条商品全数据
     */
    public function goodsOneDetail(){
    	$this->V(['goods_id'=>['egNum']]);
        $id = intval($_POST['goods_id']);
        $goods = $this->table('groupbuy_goods')->where(['id'=>$id,'is_on'=>1])->get(null,true);
        if(!$goods){
            $this->R('',70009);
        }
        $albumList = explode(";",$goods['goods_album'] );
        /*$count=count($albumList);
        for ($i=0; $i < $count-1; $i++) { 
            $goods['img']=$albumList[$i];
        }*/
        $goods['img'] = $albumList;
        unset($goods['goods_album']);
        $goods['add_time'] = date('Y-m-d H:i:s',$goods['add_time']);
        $goods['update_time'] = date('Y-m-d H:i:s',$goods['update_time']);

        $this->R(['goods'=>$goods]);
    }
    /**
     * 查询一条商品列表(分页)
     */
    public function goodsList(){
        $pageInfo = $this->P();
        $file = ['id','goods_title','goods_name','goods_album','price','market_price','cost_price','stock','discount_rate','add_time'];

        $class = $this->table('groupbuy_goods')->where(['is_on'=>1])->order('add_time desc');

        //查询并分页
        $goodspage = $this->getOnePageData($pageInfo,$class,'get','getListLength',[$file],false);
        if($goodspage ){
            foreach ($goodspage  as $k=>$v){
                $status = $this->table('groupbuy_group')->where(['is_on'=>1,'goods_id'=>$v['id']])->get(['id'],false);
                $goodspage [$k]['group_num'] = count($status);
                $status = $this->table('groupbuy_bill')->where(['is_on'=>1,'goods_id'=>$v['id'],'status'=>2])->get(['id'],false);
                $goodspage [$k]['sales'] = count($status);
                $goodspage [$k]['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
            }
        }else{
            $goodspage  = null;
        }
        //返回数据，参见System/BaseClass.class.php方法
        $this->R(['goodspage'=>$goodspage,'pageInfo'=>$pageInfo]);
        
    }
	/**
    *删除商品（设置数据库字段为0，相当于回收站）
    *商品id
     */
    public function goodsOneDelete(){
    
        $this->V(['goods_id'=>['egNum']]);
        $id = intval($_POST['goods_id']);
        $goods = $this->table('groupbuy_goods')->where(['id'=>$id,'is_on'=>1])->get(['id'],true);
        if(!$goods){
            $this->R('',70009);
        }

        $code = $this->table('groupbuy_goods')->where(['id'=>$id,'is_on'=>1])->update(['is_on'=>0]);
        if(!$code){
            $this->R('',40001);
        }

        $this->R();
    }
    /**
     *删除商品（清除数据）
     *商品id
     */
    public function goodsOneDeleteConfirm(){
    
       $this->V(['goods_id'=>['egNum']]);
        $id = intval($_POST['goods_id']);
        $goods = $this->table('groupbuy_goods')->where(['id'=>$id,'is_on'=>1])->get(['id'],true);
        if(!$goods){
            $this->R('',70009);
        }
        //删除图片文件
        $album = $this->table('groupbuy_goods')->where(['id'=>$goodsId])->get(['goods_album'],true);
        if ($album) {
            $goodsUrlArray = explode(";",$album['goods_album'] );
            $number=count($goodsUrlArray)-1;
            for ($i=0; $i <$number; $i++) {
                    @unlink("../html".$goodsUrlArray[$i]);
                }
            }

        $code = $this->table('groupbuy_goods')->where(['id'=>$id,'is_on'=>1])->delete();
       

        $this->R();
    }
    //增加相册图片
    public function albumAdd(){
        $goodsId = intval($_POST['goods_id']);
        $pictureAlbum = $_FILES;
        $imgarray = $this->H('PictureUpload')->pictureUploadMore($pictureAlbum,'groupbuyAlbum',false);
        $data['goods_album'] = $imgarray['img'];
        $goods = $this->table('groupbuy_goods')->where(['id'=>$goodsId])->update($data);
            if(!$goods){
                $this->R('',40001);
            }
            $this->R();
    }
    //删除相册的图片
    public function albumDel(){
        $goodsId = intval($_POST['goods_id']);
        $album = $this->table('groupbuy_goods')->where(['id'=>$goodsId])->get(['goods_album'],true);
        if ($album) {
            $goodsUrlArray = explode(";",$album['goods_album'] );
            $number=count($goodsUrlArray)-1;
            for ($i=0; $i <$number; $i++) {
                    @unlink("../html".$goodsUrlArray[$i]);
                }
            }
            $data['goods_album'] =null;
            $goods = $this->table('groupbuy_goods')->where(['id'=>$goodsId])->update($data);
            $this->R();
        }
    //相册图片列表
    public function albumList(){
        $goodsId = intval($_POST['goods_id']);
        $album = $this->table('groupbuy_goods')->where(['id'=>$goodsId])->get(['goods_album'],true);
        if ($album['goods_album']=="") {
            $albumList = null;
        }else{
        $albumList = explode(";",$album['goods_album'] );
        unset($albumList[6]);
        }
        
        $this->R(['albumList'=>$albumList]);
    }
}