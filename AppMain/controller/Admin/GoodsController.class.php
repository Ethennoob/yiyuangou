<?php
/**
 * 一元购系统---商品管理类
 * @authors 凌翔 (553299576@qq.com)
 * @date    2015-11-26 13:11:35
 * @version $Id$
 */

namespace AppMain\controller\Admin;
use \System\BaseClass;

class GoodsController extends BaseClass {
    /**
     * 添加商品
     * 名称，标题，成本价，销售价，所属专题，是否免运费
     */
    public function goodsAdd()
        {
            $rule = [
                    // 'thematic_id'    =>['egNum'],
                    'goods_name'     =>[],
                    // 'goods_title'    =>[],
                    // 'goods_desc'     =>[],
                    // 'cost_price'     =>['money'],
                    // 'price'          =>['money'],
                    // 'free_post'      =>['in',[0,1],true],
                    // 'goods_album'  => [],
                ];
                $this->V($rule); 

                foreach ($rule as $k => $v) {
                        $data[$k] = $_POST[$k];
                }
<<<<<<< .mine
            $data['goods_sn']=date("Ymd").rand(100000,999999);
            $rate = $this->table('system')->where(['id'=>1])->get(['buy_limit'],true);
            $data['limit_num'] = $data['price']*$rate['buy_limit']*0.01;
            $data['add_time'] = time();
            $data['upload_date'] = time();//上架时间
||||||| .r8
            $data['goods_sn']=date("Ymd").rand(100000,999999);
            $data['limit_num'] = $data['price'];
            $data['add_time'] = time();
            $data['upload_date'] = time();//上架时间
=======
            // $data['goods_sn']=date("Ymd").rand(100000,999999);
            // $data['limit_num'] = $data['price'];
            // $data['add_time'] = time();
            // $data['upload_date'] = time();//上架时间
>>>>>>> .r11
            //图片上传
            $pictureName = $_FILES['goods_img'];
            $imgarray = $this->H('PictureUpload')->pictureUpload($pictureName,'goods',true);
            $data['goods_img'] = $imgarray['img'];
            $data['goods_thumb'] = $imgarray['thumb'];
<<<<<<< .mine
           /* //相册
||||||| .r8
            //相册
=======

            $pictureName = $_FILES['goods_album'][1];
            print_r(count($pictureName));
            // $imgarray = $this->H('PictureUpload')->pictureUpload($pictureName,'goodss',true);
            // $data['goods_album'] = $imgarray['img'];
            //相册
>>>>>>> .r11
            $pictureAlbum = $_FILES;
            $imgarray = $this->H('PictureUpload')->pictureUploadMore($pictureAlbum,'goodsAlbum',false);
            $data['goods_album'] = $imgarray['img'];*/

                $goods = $this->table('goods')->save($data);
                if(!$goods){
                    $this->R('',40001);
                }
<<<<<<< .mine
            $goods_id = $this->table('goods')->where(['goods_sn'=>$data['goods_sn'],'is_on'=>1])->get(['id'],true);
            foreach ($goods_id as $k => $v) {           
            }
            $thematic_id=$data['thematic_id'];
            $number=$data['price'];
            $createCode=$this->createCode($v,$thematic_id,$number);
||||||| .r8
            $goods_id = $this->table('goods')->where(['goods_sn'=>$data['goods_sn'],'is_on'=>1])->get(['id'],true);
            foreach ($goods_id as $k => $v) {           
            }
            $thematic_id=$data['thematic_id'];
            $number=$data['limit_num'];
            $createCode=$this->createCode($v,$thematic_id,$number);
=======
            // $goods_id = $this->table('goods')->where(['goods_sn'=>$data['goods_sn'],'is_on'=>1])->get(['id'],true);
            // foreach ($goods_id as $k => $v) {           
            // }
            // $thematic_id=$data['thematic_id'];
            // $number=$data['limit_num'];
            // $createCode=$this->createCode($v,$thematic_id,$number);
>>>>>>> .r11
                      

            $this->R(); 
        }     
        /**
	    * 自动增加认购码
	    */
		private function createCode($goods_id,$thematic_id,$number){
			
			$code=array();
            $codeCreate=$this->H('Coupon')->generate_promotion_code($number,null,15);
            for ($i=0; $i <$number ; $i++) { 
            	$data[$i] = array(
            		    'goods_id'   => $goods_id,
            			'thematic_id' => $thematic_id,
            			'code' => $codeCreate[$i],
            			'key'   => $i+1,
            			'add_time' => time(),
            		);
            	$code = $this->table('code')->save($data[$i]);
            	if (!$code) {
            		$this->R('',80001);
            	}
            }
            
            $this->R();
		} 
	/**
	 * 商品修改
	 * 商品名称，商品标题，商品详细，成本价，售价，是否包邮
	 */
	public function goodsOneEdit()
        {  
            $rule = [
                    'id'             =>['egNum'],
                    'goods_name'   =>[],
                    'goods_title'    =>[],
                    'goods_desc'     =>[],
                    'cost_price'     =>['money'],
                    'price'          =>['money'],
                    'free_post'      =>['in',[0,1],true],
                ];
                $this->V($rule); 

                foreach ($rule as $k => $v) {
                        $data[$k] = $_POST[$k];
                }
            $data['limit_num'] = $data['price'];
            $data['update_time'] = time();
            //删除图片文件
            $goodsImg = $this->table('goods')->where(['id'=>$data['id'],'is_on'=>1])->get(['goods_img','goods_thumb'],true);
            //分割图片url，匹配图片名是否相同，同则不删除，不同则删除
            $goodsImgName= explode("/",$goodsImg['goods_img']);

            if($goodsImgName['4']!=$_FILES['goods_img']['name']){
                @unlink("../html".$goodsImg['goods_img']);
                @unlink("../html".$goodsImg['goods_thumb']);
                $pictureName=$_FILES['goods_img'];
                $imgarray=$this->H('PictureUpload')->pictureUpload($pictureName,'goods',true);
                $data['goods_img']=$imgarray['img'];
                $data['goods_thumb']=$imgarray['thumb'];
            }else{
                $imgarray['img']=$goodsImg['goods_img'];
                $imgarray['thumb']=$goodsImg['goods_thumb'];
                $data['goods_img']=$imgarray['img'];
                $data['goods_thumb']=$imgarray['thumb'];
            }
            
            //相册修改待定
            /*$pictureAlbum = $_FILES['goods_album'];
            $imgarray = $this->H('PictureUpload')->pictureUploadMore($pictureName,'goodsAlbum',false);
            $data['goods_album'] = $imgarray['img'];*/

                $goods = $this->table('goods')->where(['id'=>$data['id']])->update($data);
                if(!$goods){
                    $this->R('',40001);
                }
            $this->R(); 
        }
    /**
     * 查询一条商品全数据
     */
    public function goodsOneDetail(){
    	$this->V(['id'=>['egNum']]);
        $id = intval($_POST['id']);
        $goods = $this->table('goods')->where(['id'=>$id,'is_on'=>1])->get(null,true);
        if(!$goods){
            $this->R('',70009);
        }
        $goods['add_time'] = date('Y-m-d H:i:s',$goods['add_time']);
        $goods['update_time'] = date('Y-m-d H:i:s',$goods['update_time']);
        $goods['upload_date'] = date('Y-m-d H:i:s',$goods['upload_date']);

        $this->R(['goods'=>$goods]);
    }
    /**
     * 查询一条商品列表(分页)
     */
    public function goodsList(){
        $pageInfo = $this->P();
        $file = ['id','goods_sn','thematic_id','goods_thumb','goods_name','cost_price','price','limit_num','add_time'];

        $class = $this->table('goods')->where(['is_on'=>1])->order('add_time desc');

        //查询并分页
        $goodspage = $this->getOnePageData($pageInfo,$class,'get','getListLength',[$file],false);
        if($goodspage ){
            foreach ($goodspage  as $k=>$v){
                $goodspage [$k]['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
                $status = $this->table('thematic')->where(['is_on'=>1,'id'=>$v['thematic_id']])->get(['thematic_name'],true);
                $goodspage [$k]['thematic'] = $status['thematic_name'];
                unset($goodspage [$k]['thematic_id']);
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
    
        $this->V(['id'=>['egNum']]);
        $id = intval($_POST['id']);
        $goods = $this->table('goods')->where(['id'=>$id,'is_on'=>1])->get(['id'],true);
    
        if(!$goods){
            $this->R('',70009);
        }
         
        $code = $this->table('code')->where(['goods_id'=>$id,'is_on'=>1])->get(['id'],true);
    
        if(!$code){
            $this->R('',70009);
        }

        $code = $this->table('goods')->where(['id'=>$id,'is_on'=>1])->update(['is_on'=>0]);
        if(!$code){
            $this->R('',40001);
        }
        $code = $this->table('code')->where(['goods_id'=>$id])->update(['is_on'=>0]);
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
    
       $this->V(['id'=>['egNum']]);
        $id = intval($_POST['id']);
        //删除图片文件
        $goodsImg = $this->table('goods')->where(['id'=>$id,'is_on'=>1])->get(['goods_img','goods_thumb'],true);
        //分割图片url，匹配图片名是否相同，同则不删除，不同则删除
            @unlink("../html".$goodsImg['goods_img']);
            @unlink("../html".$goodsImg['goods_thumb']);
       
        $goods = $this->table('goods')->where(['id'=>$id,'is_on'=>1])->get(['id'],true);
    
        if(!$goods){
            $this->R('',70009);
        }
         
        $code = $this->table('code')->where(['goods_id'=>$id,'is_on'=>1])->get(['id'],true);
    
        if(!$code){
            $this->R('',70009);
        }

        $code = $this->table('goods')->where(['id'=>$id,'is_on'=>1])->delete();
        if(!$code){
            $this->R('',40001);
        }
        $code = $this->table('code')->where(['goods_id'=>$id])->delete();
        if(!$code){
            $this->R('',40001);
        }
       

        $this->R();
    }
    //增加相册图片
    public function albumAdd(){
        $goodsId = intval($_POST['id']);
        $pictureAlbum = $_FILES;
        $imgarray = $this->H('PictureUpload')->pictureUploadMore($pictureAlbum,'goodsAlbum',false);
        $data['goods_album'] = $imgarray['img'];
        $goods = $this->table('goods')->where(['id'=>$goodsId])->update($data);
            if(!$goods){
                $this->R('',40001);
            }
            $this->R();
    }
    //删除相册的图片
    public function albumDel(){
        $goodsId = intval($_POST['id']);
        $album = $this->table('goods')->where(['id'=>$goodsId])->get(['goods_album'],true);
        if ($album) {
            $goodsUrlArray = explode(";",$album['goods_album'] );
            $number=count($goodsUrlArray)-1;
            for ($i=0; $i <$number; $i++) {
                    @unlink("../html".$goodsUrlArray[$i]);
                }
            }
            $data['goods_album'] =null;
            $goods = $this->table('goods')->where(['id'=>$goodsId])->update($data);
            $this->R();
        }
    //相册图片列表
    public function albumList(){
        $goodsId = intval($_POST['id']);
        $album = $this->table('goods')->where(['id'=>$goodsId])->get(['goods_album'],true);
        if (!$album) {
            $this->R('',70009);
        }
        $albumList = explode(";",$album['goods_album'] );
        $this->R(['albumList'=>$albumList]);
    }
}