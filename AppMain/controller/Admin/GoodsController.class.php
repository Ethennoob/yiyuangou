<?php

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
                    'company_id'     =>['egNum'],
                    'thematic_id'    =>['egNum'],
                    'goods_name'     =>[],
                    'goods_title'    =>[],
                    'goods_desc'     =>[],
                    'cost_price'     =>['money'],
                    'nature'         =>['in',[0,1],true],
                    'price'          =>['money'],
                    'free_post'      =>['in',[0,1],true],
                    //'goods_album'  => [],
                ];
                $this->V($rule); 

                foreach ($rule as $k => $v) {
                        $data[$k] = $_POST[$k];
                }

            $data['goods_sn']=date("Ymd").rand(100000,999999);
            $rate = $this->table('system')->where(['id'=>1])->get(['buy_limit'],true);
            $data['limit_num'] = $data['price']*$rate['buy_limit']*0.01;
            $data['add_time'] = time();
            $data['upload_date'] = time();//上架时间



            //图片上传
            $pictureName = $_FILES['goods_img'];
            $imgarray = $this->H('PictureUpload')->pictureUpload($pictureName,'goods',true);
            $data['goods_img'] = $imgarray['img'];
            $data['goods_thumb'] = $imgarray['thumb'];

                $goods = $this->table('goods')->save($data);
                if(!$goods){
                    $this->R('',40001);
                }

            $goods_id = $this->table('goods')->where(['goods_sn'=>$data['goods_sn'],'is_on'=>1])->get(['id'],true);
            foreach ($goods_id as $k => $v) {           
            }
            $thematic_id=$data['thematic_id'];
            $number=$data['price'];
            $createCode=$this->createCode($v,$thematic_id,$number);

                      

            $this->R(); 
        }     
        /**
	    * 自动增加认购码
	    */
		private function createCode($goods_id,$thematic_id,$number){
			
			$code=array();
            $codeCreate=$this->H('Code')->generate_promotion_new_code($number);
            for ($i=1; $i <=$number ; $i++) { 
            	$data[$i] = array(
            		    'goods_id'   => $goods_id,
            			'thematic_id' => $thematic_id,
            			'code' => $codeCreate[$i],
            			'key'   => $i,
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
                    'company_id'     =>['egNum'],
                    'goods_id'       =>['egNum'],
                    'thematic_id'    =>['egNum'],
                    'goods_name'     =>[],
                    'goods_title'    =>[],
                    'goods_desc'     =>[],
                    'cost_price'     =>['money'],
                    'nature'         =>['in',[0,1],true],
                    //'price'          =>['money'],
                    'free_post'      =>['in',[0,1],true],
                ];
                $this->V($rule); 

                foreach ($rule as $k => $v) {
                        $data[$k] = $_POST[$k];
                }
            //$data['limit_num'] = $data['price'];
            $data['update_time'] = time();
            //删除图片文件
            if(isset($_FILES['goods_img'])){
            $goodsImg = $this->table('goods')->where(['id'=>$data['goods_id'],'is_on'=>1])->get(['goods_img','goods_thumb'],true);
            //分割图片url，匹配图片名是否相同，同则不删除，不同则删除
            
                @unlink("../html".$goodsImg['goods_img']);
                @unlink("../html".$goodsImg['goods_thumb']);
                $pictureName=$_FILES['goods_img'];
                $imgarray=$this->H('PictureUpload')->pictureUpload($pictureName,'goods',true);
                $data['goods_img']=$imgarray['img'];
                $data['goods_thumb']=$imgarray['thumb'];
         
            }
            
         

                $goods = $this->table('goods')->where(['id'=>$data['goods_id']])->update($data);
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
        $goods = $this->table('goods')->where(['id'=>$id,'is_on'=>1])->get(null,true);
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
        $goods['upload_date'] = date('Y-m-d H:i:s',$goods['upload_date']);
        $status = $this->table('company')->where(['is_on'=>1,'id'=>$goods['company_id']])->get(['company_name'],true);
        $goods['company_name'] = $status['company_name'];
        $this->R(['goods'=>$goods]);
    }
    /**
     * 查询一条商品列表(分页)
     */
    public function goodsList(){
        $pageInfo = $this->P();
        if (isset($_POST['company_id'])&&isset($_POST['thematic_id'])) {
            $this->V(['company_id'=>['egNum']]);
            $company_id = intval($_POST['company_id']); 
            $this->V(['thematic_id'=>['egNum']]);
            $id = intval($_POST['thematic_id']);
            $file = ['id','goods_sn','company_id','thematic_id','goods_name','nature','cost_price','price','free_post','is_show','limit_num','add_time'];
            $class = $this->table('goods')->where(['is_on'=>1,'thematic_id'=>$id,'company_id'=>$company_id])->order('add_time desc');
        }if(isset($_POST['company_id']) && !isset($_POST['thematic_id'])){
            $this->V(['company_id'=>['egNum']]);
            $company_id = intval($_POST['company_id']);
            $file = ['id','goods_sn','company_id','thematic_id','goods_name','nature','cost_price','price','free_post','is_show','limit_num','add_time'];
            $class = $this->table('goods')->where(['is_on'=>1,'company_id'=>$company_id])->order('add_time desc');
        }elseif(!isset($_POST['company_id'])){
            $file = ['id','goods_sn','company_id','thematic_id','goods_name','nature','cost_price','price','free_post','is_show','limit_num','add_time'];
            $class = $this->table('goods')->where(['is_on'=>1])->order('add_time desc');
        }
        

        //查询并分页
        $goodspage = $this->getOnePageData($pageInfo,$class,'get','getListLength',[$file],false);
        if($goodspage ){
            foreach ($goodspage  as $k=>$v){
                $goodspage [$k]['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
                $status = $this->table('thematic')->where(['is_on'=>1,'id'=>$v['thematic_id']])->get(['thematic_name'],true);
                $goodspage [$k]['thematic_name'] = $status['thematic_name'];
                $status = $this->table('company')->where(['is_on'=>1,'id'=>$v['company_id']])->get(['company_name'],true);
                $goodspage [$k]['company_name'] = $status['company_name'];
                unset($goodspage [$k]['thematic_id']);
            }
        }else{
            $goodspage  = null;
        }
        //返回数据，参见System/BaseClass.class.php方法
        $this->R(['goodspage'=>$goodspage,'pageInfo'=>$pageInfo]);
        
    }
    /**
     * 模糊查询（商品名称）
     */
    public function goodsListName(){

        $this->V(['goods_name'=>[]]);
        $goods_name = $_POST['goods_name'];
        if ((empty($_POST['thematic_id'])||!isset($_POST['thematic_id']))&&(empty($_POST['company_id'])||!isset($_POST['company_id']))) {
            $where = 'is_on = 1 and goods_name like "%'.$goods_name.'%"';
        }elseif(empty($_POST['thematic_id'])||!isset($_POST['thematic_id'])){
            $this->V(['company_id'=>['egNum']]);
            $company_id = $_POST['company_id'];
            $where = 'is_on = 1 and company_id = "'.$company_id.'" and goods_name like "%'.$goods_name.'%"';
        }else{
            $this->V(['company_id'=>['egNum']]);
            $company_id = $_POST['company_id'];
            $this->V(['thematic_id'=>['egNum']]);
            $thematic_id = $_POST['thematic_id'];
            $where = 'is_on = 1 and company_id = "'.$company_id.'" and thematic_id = "'.$thematic_id.'" and goods_name like "%'.$goods_name.'%"';
        }
       
        $pageInfo = $this->P();
        $file = ['id','goods_sn','thematic_id','goods_name','cost_price','price','free_post','is_show','limit_num','add_time'];
        //$where = 'is_on = 1 and goods_name like "%'.$goods_name.'%"';
        $class = $this->table('goods')->where($where)->order('add_time desc');

        //查询并分页
        $goodspage = $this->getOnePageData($pageInfo,$class,'get','getListLength',[$file],false);
        if($goodspage ){
            foreach ($goodspage  as $k=>$v){
                $goodspage [$k]['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
                $status = $this->table('thematic')->where(['is_on'=>1,'id'=>$goodspage[$k]['id']])->get(['thematic_name'],true);
                $goodspage [$k]['thematic_name'] = $status['thematic_name'];
                unset($goodspage [$k]['thematic_id']);
            }
        }else{
            $goodspage  = null;
        }
        //返回数据，参见System/BaseClass.class.php方法
        $this->R(['goodspage'=>$goodspage,'pageInfo'=>$pageInfo]);
        
    }
    /**
     * 模糊查询（商品名称）
     */
    public function goodsListSn(){
        $this->V(['goods_sn'=>[]]);
        $goods_sn = $_POST['goods_sn'];
        if ((empty($_POST['thematic_id'])||!isset($_POST['thematic_id']))&&(empty($_POST['company_id'])||!isset($_POST['company_id']))) {
            $where = 'is_on = 1 and goods_sn like "%'.$goods_sn.'%"';
        }elseif(empty($_POST['thematic_id'])||!isset($_POST['thematic_id'])){
            $this->V(['company_id'=>['egNum']]);
            $company_id = $_POST['company_id'];
            $where = 'is_on = 1 and company_id = "'.$company_id.'" and goods_sn like "%'.$goods_sn.'%"';
        }else{
            $this->V(['company_id'=>['egNum']]);
            $company_id = $_POST['company_id'];
            $this->V(['thematic_id'=>['egNum']]);
            $thematic_id = $_POST['thematic_id'];
            $where = 'is_on = 1 and company_id = "'.$company_id.'" and thematic_id = "'.$thematic_id.'" and goods_sn like "%'.$goods_sn.'%"';
        }
        $pageInfo = $this->P();
        $file = ['id','goods_sn','thematic_id','goods_name','cost_price','price','free_post','is_show','limit_num','add_time'];
        //$where = 'is_on = 1 and goods_sn like "%'.$goods_sn.'%"';
        $class = $this->table('goods')->where($where)->order('add_time desc');

        //查询并分页
        $goodspage = $this->getOnePageData($pageInfo,$class,'get','getListLength',[$file],false);
        if($goodspage ){
            foreach ($goodspage  as $k=>$v){
                $goodspage [$k]['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
                $status = $this->table('thematic')->where(['is_on'=>1,'id'=>$goodspage[$k]['id']])->get(['thematic_name'],true);
                $goodspage [$k]['thematic_name'] = $status['thematic_name'];
                unset($goodspage [$k]['thematic_id']);
            }
        }else{
            $goodspage  = null;
        }
        //返回数据，参见System/BaseClass.class.php方法
        $this->R(['goodspage'=>$goodspage,'pageInfo'=>$pageInfo]);
        
    }
    /**
     * 模糊查询（专题名称）
     */
    public function goodsListThematic(){
        $this->V(['thematic_name'=>[]]);
        $thematic_name = $_POST['thematic_name'];
        if ((empty($_POST['thematic_id'])||!isset($_POST['thematic_id']))&&(empty($_POST['company_id'])||!isset($_POST['company_id']))) {
            $where = 'is_on = 1 and thematic_name like "%'.$thematic_name.'%"';
        }elseif(empty($_POST['thematic_id'])||!isset($_POST['thematic_id'])){
            $this->V(['company_id'=>['egNum']]);
            $company_id = $_POST['company_id'];
            $where = 'is_on = 1 and company_id = "'.$company_id.'" and thematic_name like "%'.$thematic_name.'%"';
        }else{
            $this->V(['company_id'=>['egNum']]);
            $company_id = $_POST['company_id'];
            $this->V(['thematic_id'=>['egNum']]);
            $thematic_id = $_POST['thematic_id'];
            $where = 'is_on = 1 and company_id = "'.$company_id.'" and thematic_id = "'.$thematic_id.'" and thematic_name like "%'.$thematic_name.'%"';
        }
        //$where = 'is_on = 1 and thematic_name like "%'.$thematic_name.'%"';
        $thematic = $this->table('thematic')->where($where)->get(['id'],true);
        $pageInfo = $this->P();
        $file = ['id','goods_sn','thematic_id','goods_name','cost_price','price','free_post','is_show','limit_num','add_time'];
        
       $class = $this->table('goods')->where(['is_on'=>1,'thematic_id'=>$thematic['id']])->order('add_time desc');

        //查询并分页
        $goodspage = $this->getOnePageData($pageInfo,$class,'get','getListLength',[$file],false);
        if($goodspage ){
            foreach ($goodspage  as $k=>$v){
                $goodspage [$k]['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
                $status = $this->table('thematic')->where(['is_on'=>1,'id'=>$goodspage[$k]['id']])->get(['thematic_name'],true);
                $goodspage [$k]['thematic_name'] = $status['thematic_name'];
                unset($goodspage [$k]['thematic_id']);
            }
        }else{
            $goodspage  = null;
        }
        //返回数据，参见System/BaseClass.class.php方法
        $this->R(['goodspage'=>$goodspage,'pageInfo'=>$pageInfo]);
        
    }
    /**
     * 模糊查询（是否实物 0为实物 1为虚拟券）
     */
    public function goodsListNature(){
        $this->V(['nature'=>['num']]);
        $nature = $_POST['nature'];
        if ((empty($_POST['thematic_id'])||!isset($_POST['thematic_id']))&&(empty($_POST['company_id'])||!isset($_POST['company_id']))) {
            $where = 'is_on = 1 and nature = "'.$nature.'"';
        }elseif(empty($_POST['thematic_id'])||!isset($_POST['thematic_id'])){
            $this->V(['company_id'=>['egNum']]);
            $company_id = $_POST['company_id'];
            $where = 'is_on = 1 and company_id = "'.$company_id.'" and nature = "'.$nature.'"';
        }else{
            $this->V(['company_id'=>['egNum']]);
            $company_id = $_POST['company_id'];
            $this->V(['thematic_id'=>['egNum']]);
            $thematic_id = $_POST['thematic_id'];
            $where = 'is_on = 1 and company_id = "'.$company_id.'" and thematic_id = "'.$thematic_id.'" and nature = "'.$nature.'"';
        }
        $pageInfo = $this->P();
        $file = ['id','goods_sn','thematic_id','goods_name','cost_price','price','free_post','is_show','limit_num','add_time'];
        //$where = 'is_on = 1 and nature like "%'.$nature.'%"';
        $class = $this->table('goods')->where($where)->order('add_time desc');

        //查询并分页
        $goodspage = $this->getOnePageData($pageInfo,$class,'get','getListLength',[$file],false);
        if($goodspage ){
            foreach ($goodspage  as $k=>$v){
                $goodspage [$k]['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
                $status = $this->table('thematic')->where(['is_on'=>1,'id'=>$goodspage[$k]['id']])->get(['thematic_name'],true);
                $goodspage [$k]['thematic_name'] = $status['thematic_name'];
                unset($goodspage [$k]['thematic_id']);
            }
        }else{
            $goodspage  = null;
        }
        //返回数据，参见System/BaseClass.class.php方法
        $this->R(['goodspage'=>$goodspage,'pageInfo'=>$pageInfo]);
    }
    /**
     * 模糊查询（价格）
     */
    public function goodsListPrice(){
        $this->V(['price'=>['num']]);
        $price = $_POST['price'];
        if ((empty($_POST['thematic_id'])||!isset($_POST['thematic_id']))&&(empty($_POST['company_id'])||!isset($_POST['company_id']))) {
            $where = 'is_on = 1 and price = "'.$price.'"';
        }elseif(empty($_POST['thematic_id'])||!isset($_POST['thematic_id'])){
            $this->V(['company_id'=>['egNum']]);
            $company_id = $_POST['company_id'];
            $where = 'is_on = 1 and company_id = "'.$company_id.'" and price = "'.$price.'"';
        }else{
            $this->V(['company_id'=>['egNum']]);
            $company_id = $_POST['company_id'];
            $this->V(['thematic_id'=>['egNum']]);
            $thematic_id = $_POST['thematic_id'];
            $where = 'is_on = 1 and company_id = "'.$company_id.'" and thematic_id = "'.$thematic_id.'" and price = "'.$price.'"';
        }
        $pageInfo = $this->P();
        $file = ['id','goods_sn','thematic_id','goods_name','cost_price','price','free_post','is_show','limit_num','add_time'];
        //$where = 'is_on = 1 and price like "%'.$price.'%"';
        $class = $this->table('goods')->where($where)->order('add_time desc');

        //查询并分页
        $goodspage = $this->getOnePageData($pageInfo,$class,'get','getListLength',[$file],false);
        if($goodspage ){
            foreach ($goodspage  as $k=>$v){
                $goodspage [$k]['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
                $status = $this->table('thematic')->where(['is_on'=>1,'id'=>$goodspage[$k]['id']])->get(['thematic_name'],true);
                $goodspage [$k]['thematic_name'] = $status['thematic_name'];
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
    
        $this->V(['goods_id'=>['egNum']]);
        $id = intval($_POST['goods_id']);
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
        $code = $this->table('purchase')->where(['goods_id'=>$id,'is_on'=>1])->update(['is_on'=>0]);
        if(!$code){
            $this->R('',70009);
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
        $code = $this->table('purchase')->where(['goods_id'=>$id,'is_on'=>1])->delete();
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
        $goodsId = intval($_POST['goods_id']);
        /*$pictureAlbum = $_FILES;
        $imgarray = $this->H('PictureUpload')->pictureUploadMore($pictureAlbum,'goodsAlbum',false);
        $data['goods_album'] = $imgarray['img'];*/
        //图片上传
        $albumurl = $this->table('goods')->where(['is_on'=>1,'id'=>$goodsId])->get(['goods_album'],true);
        $pictureName = $_FILES['goods_album'];
        $imgarray = $this->H('PictureUpload')->pictureUpload($pictureName,'goods',true);
        if ($albumurl['goods_album']==null) {
        $data['goods_album'] = $imgarray['img'];
        $goods = $this->table('goods')->where(['id'=>$goodsId])->update($data);
            if(!$goods){
                $this->R('',40001);
            }
            $this->R(); 
        }else{
            $data['goods_album'] = $albumurl['goods_album'].";".$imgarray['img'];
            $goods = $this->table('goods')->where(['id'=>$goodsId])->update($data);
            $this->R();
        }
    }
    //删除相册的图片
    public function albumDel(){
        $goodsId = intval($_POST['goods_id']);

        $album = $this->table('goods')->where(['is_on'=>1,'id'=>$goodsId])->get(['goods_album'],true);
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
        $goodsId = intval($_POST['goods_id']);
        $album = $this->table('goods')->where(['id'=>$goodsId])->get(['goods_album'],true);
        if ($album['goods_album']=="") {
            $albumList = null;
        }else{
        $albumList = explode(";",$album['goods_album'] );
        //unset($albumList[6]);
        }
        
        $this->R(['albumList'=>$albumList]);
    }
}