<?php
/**
 * 一元购系统---专题管理类
 * @authors 凌翔 (553299576@qq.com)
 * @date    2015-11-25 22:04:46
 * @version $Id$
 */

namespace AppMain\controller\Admin;
use \System\BaseClass;

class ThematicController extends BaseClass {
    
    /**
    *添加专题
    *专题编号，专题名称，专题海报
     */
    public function thematicOneAdd(){
        //-----------字段验证-----------
        $rule = [
            'company_id'      =>['egNum'],
            'thematic_name'   =>[],
            //'nature'          =>['in',[0,1]],
            //'poster'          =>[],
        ];
        $this->V($rule);
        
        foreach ($rule as $k=>$v){
            $data[$k] = $_POST[$k];
        }
         //图片上传
        /*$pictureName = $_FILES['poster'];
        $imgarray = $this->H('PictureUpload')->pictureUpload($pictureName,'poster',false);*/

        $data['add_time']     = time();
        $data['update_time']     = time();
        //$data['poster'] = $imgarray['img'];
    
        $thematic = $this->table('thematic')->save($data);
        if(!$thematic){
            $this->R('',40001);
        }
    
        $this->R();
    }
     /**
     * 修改专题信息
     */
    public function thematicOneEdit(){
       $rule = [
            'company_id'     =>['egNum'],
            'id'              =>['egNum'],//已有会员id
            'thematic_name'   =>[],
            'status'           =>['in',[0,2]],
            //'nature'          =>['in',[0,1]],
            'is_show'          =>['in',[0,1]],
            //'poster'          =>[],
        ];
        $this->V($rule);
        $id = intval($_POST['id']);

        $thematic = $this->table('thematic')->where(['id'=>$id,'is_on'=>1])->get(['id'],true);
        if(!$thematic){
            $this->R('',70009);
        }

        unset($rule['id']);
        foreach ($rule as $k=>$v){
            if(isset($_POST[$k])){
                $data[$k] = $_POST[$k];
            }
        }
        //图片上传
        /*if(isset($_FILES['poster'])){
        $pictureName = $_FILES['poster'];
        $imgarray = $this->H('PictureUpload')->pictureUpload($pictureName,'poster',false);
        //删除图片文件
        $pic_url = $this->table('thematic')->where(['id'=>$id,'is_on'=>1])->get(['poster'],true);
        foreach ($pic_url as $key => $v) {
             @unlink("../html".$v);
         }
       
        $data['poster'] = $imgarray['img'];
            
        }*/

        $thematic = $this->table('thematic')->where(['id'=>$id])->update($data);
        if(!$thematic){
            $this->R('',40001);
        }

        $this->R();
    }
    /**
     * 模糊查询专题列表(通过专题名)
     */
    public function thematicListName(){
        $pageInfo = $this->P();
        $this->V(['thematic_name'=>[]]);
        $thematic_name = $_POST['thematic_name'];
        $file = ['id','thematic_name','nature','status','is_show','add_time'];
        $where = 'is_on = 1 and thematic_name like "%'.$thematic_name.'%"';
        $class = $this->table('thematic')->where($where)->order('add_time desc');

        //查询并分页
        $thematicpage = $this->getOnePageData($pageInfo,$class,'get','getListLength',[$file],false);
        if($thematicpage ){
            foreach ($thematicpage  as $k=>$v){
                $thematicpage [$k]['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
                /*$goodsarr = $this->table('goods')->where(['is_on'=>1,'thematic_id'=>$v['id']])->get(['goods_name','price','limit_num'],false);*/
                $file = ['id','goods_name','price','limit_num','add_time'];
                $class = $this->table('goods')->where(['is_on'=>1,'thematic_id'=>$v['id']])->order('add_time desc');
                //查询并分页
                $goodspage = $this->getOnePageData($pageInfo,$class,'get','getListLength',[$file],false);
                if(!$goodspage){
                    $goodspage=null;
                }
                /*$count = count($goodsarr);
                    for ($i=0; $i < $count; $i++) { 
                $v = implode(",",$goodsarr[$i]); //可以用implode将一维数组转换为用逗号连接的字符串
                $temp[] = $v;
            }*/
            $thematicpage[$k]['goods']=$goodspage;
            unset($goodspage);
            }
        }else{
            $thematicpage  = null;
        }
        //返回数据，参见System/BaseClass.class.php方法
        $this->R(['thematicpage'=>$thematicpage,'pageInfo'=>$pageInfo]);
        
    }
    /**
     * 模糊查询专题列表(通过专题状态：0进行中,1即将揭晓,2已揭晓)
     */
    public function thematicListStatus(){
        $pageInfo = $this->P();
        $this->V(['status'=>['num']]);
        $status = $_POST['status'];
        $file = ['id','thematic_name','nature','status','is_show','add_time'];
        $where = 'is_on = 1 and status = "'.$status.'"';
        $class = $this->table('thematic')->where($where)->order('add_time desc');

        //查询并分页
        $thematicpage = $this->getOnePageData($pageInfo,$class,'get','getListLength',[$file],false);
        if($thematicpage ){
            foreach ($thematicpage  as $k=>$v){
                $thematicpage [$k]['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
                /*$goodsarr = $this->table('goods')->where(['is_on'=>1,'thematic_id'=>$v['id']])->get(['goods_name','price','limit_num'],false);*/
                $file = ['id','goods_name','price','limit_num','add_time'];
                $class = $this->table('goods')->where(['is_on'=>1,'thematic_id'=>$v['id']])->order('add_time desc');
                //查询并分页
                $goodspage = $this->getOnePageData($pageInfo,$class,'get','getListLength',[$file],false);
                if(!$goodspage){
                    $goodspage=null;
                }
                /*$count = count($goodsarr);
                    for ($i=0; $i < $count; $i++) { 
                $v = implode(",",$goodsarr[$i]); //可以用implode将一维数组转换为用逗号连接的字符串
                $temp[] = $v;
            }*/
            $thematicpage[$k]['goods']=$goodspage;
            unset($goodspage);
            }
        }else{
            $thematicpage  = null;
        }
        //返回数据，参见System/BaseClass.class.php方法
        $this->R(['thematicpage'=>$thematicpage,'pageInfo'=>$pageInfo]);
        
    }
    /**
     * 模糊查询专题列表(通过专题添加时间)
     */
    public function thematicListTime(){
        $pageInfo = $this->P();
        $this->V(['add_time'=>[]]);
        $add_time = $_POST['add_time'];
        $time1 = intval(strtotime($add_time));
        $time2 = $time1+24*3600;
        $file = ['id','thematic_name','nature','status','is_show','add_time'];
        $where = 'is_on = 1 and add_time > "'.$time1.'" and add_time < "'.$time1.'"';
        $class = $this->table('thematic')->where($where)->order('add_time desc');

        //查询并分页
        $thematicpage = $this->getOnePageData($pageInfo,$class,'get','getListLength',[$file],false);
        if($thematicpage ){
            foreach ($thematicpage  as $k=>$v){
                $thematicpage [$k]['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
                /*$goodsarr = $this->table('goods')->where(['is_on'=>1,'thematic_id'=>$v['id']])->get(['goods_name','price','limit_num'],false);*/
                $file = ['id','goods_name','price','limit_num','add_time'];
                $class = $this->table('goods')->where(['is_on'=>1,'thematic_id'=>$v['id']])->order('add_time desc');
                //查询并分页
                $goodspage = $this->getOnePageData($pageInfo,$class,'get','getListLength',[$file],false);
                if(!$goodspage){
                    $goodspage=null;
                }
                /*$count = count($goodsarr);
                    for ($i=0; $i < $count; $i++) { 
                $v = implode(",",$goodsarr[$i]); //可以用implode将一维数组转换为用逗号连接的字符串
                $temp[] = $v;
            }*/
            $thematicpage[$k]['goods']=$goodspage;
            unset($goodspage);
            }
        }else{
            $thematicpage  = null;
        }
        //返回数据，参见System/BaseClass.class.php方法
        $this->R(['thematicpage'=>$thematicpage,'pageInfo'=>$pageInfo]);
        
    }
    /**
     * 查询专题列表(分页)
     */
    public function thematicList(){
        $this->V(['company_id'=>['egNum',null,true]]);
        $id = intval($_POST['company_id']);
        $pageInfo = $this->P();
        $file = ['id','thematic_name','nature','status','is_show','add_time'];

        $class = $this->table('thematic')->where(['is_on'=>1,'company_id'=>$id])->order('add_time desc');

        //查询并分页
        $thematicpage = $this->getOnePageData($pageInfo,$class,'get','getListLength',[$file],false);
        if($thematicpage ){
            foreach ($thematicpage  as $k=>$v){
                $thematicpage [$k]['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
                /*$goodsarr = $this->table('goods')->where(['is_on'=>1,'thematic_id'=>$v['id']])->get(['goods_name','price','limit_num'],false);*/
                $file = ['id','goods_name','price','limit_num','add_time'];
                $class = $this->table('goods')->where(['is_on'=>1,'thematic_id'=>$v['id']])->order('add_time desc');
                //查询并分页
                $goodspage = $this->getOnePageData($pageInfo,$class,'get','getListLength',[$file],false);
                if(!$goodspage){
                    $goodspage=null;
                }
                $status = $this->table('company')->where(['is_on'=>1,'id'=>$id])->get(['company_name'],true);
                $thematicpage [$k]['company_name'] = $status['company_name'];
                /*$count = count($goodsarr);
                    for ($i=0; $i < $count; $i++) { 
                $v = implode(",",$goodsarr[$i]); //可以用implode将一维数组转换为用逗号连接的字符串
                $temp[] = $v;
            }*/
            $thematicpage[$k]['goods']=$goodspage;
            unset($goodspage);
            }
        }else{
            $thematicpage  = null;
        }
        //返回数据，参见System/BaseClass.class.php方法
        $this->R(['thematicpage'=>$thematicpage,'pageInfo'=>$pageInfo]);
        
    }
    /**
     * 查出所有专题名(status=0)
     */
    public function thematicSelect(){
        $this->V(['company_id'=>['egNum',null,true]]);
        $id = intval($_POST['company_id']);
        $thematicSelect = $this->table('thematic')->where(['is_on'=>1,'company_id'=>$id])->order('add_time desc')->get(['id','thematic_name'],false);
        
        if(!$thematicSelect){
            $thematicSelect  = null;
        }
       
        //返回数据，参见System/BaseClass.class.php方法
        $this->R(['thematicSelect'=>$thematicSelect]);
        
    }
    /**
     * 查询一条专题信息
     */
    public function thematicOneDetail(){
        $this->V(['thematic_id'=>['egNum',null,true]]);
        $id = intval($_POST['thematic_id']);
        $thematic = $this->table('thematic')->where(['id'=>$id,'is_on'=>1])->get(['id'],true);
        if(!$thematic){
            $this->R('',70009);
        }
        $thematic = $this->table('thematic')->where(['id'=>$id,'is_on'=>1])->get(null,true);
        if(!$thematic){
            $this->R('',70009);
        }
        $status = $this->table('company')->where(['is_on'=>1,'id'=>$thematic['company_id']])->get(['company_name'],true);
        $thematic['company_name'] = $status['company_name'];
        $this->R(['thematic'=>$thematic]);
    }
    /**
     * 判定首页的默认专题是否卖完了
     */
    public function changeThematic(){
        $this->V(['thematic_id'=>['egNum',null,true]]);
        $id = intval($_POST['thematic_id']);
        $thematic = $this->table('thematic')->where(['id'=>$id])->update(['status'=>2]);
        if(!$thematic){
            $this->R('',40001);
        }

        $this->R();
    }
    /*
    删除一条专题信息数据（设置数据库字段为0，相当于回收站）
     */
    public function thematicOneDelete(){
    
        $this->V(['thematic_id'=>['egNum',null,true]]);
        $id = intval($_POST['thematic_id']);
         
        $thematic = $this->table('thematic')->where(['id'=>$id,'is_on'=>1])->get(['id'],true);
    
        if(!$thematic){
            $this->R('',70009);
        }
    
        $thematic = $this->table('thematic')->where(['id'=>$id])->update(['is_on'=>0]);
        if(!$thematic){
            $this->R('',40001);
        }
        
        $goods = $this->table('goods')->where(['thematic_id'=>$id,'is_on'=>1])->get(['id'],true);
        if ($goods) {
        $code = $this->table('goods')->where(['id'=>$id,'is_on'=>1])->update(['is_on'=>0]);
        if(!$code){
            $this->R('',70009);
        }
        $code = $this->table('purchase')->where(['thematic_id'=>$id,'is_on'=>1])->update(['is_on'=>0]);
        if(!$code){
            $this->R('',70009);
        }
        $code = $this->table('code')->where(['thematic_id'=>$id])->update(['is_on'=>0]);
        if(!$code){
            $this->R('',70009);
        }
        }
        
        
        

        $this->R();
    }
    /**
     *删除一条专题信息（清除数据）
     */
    public function thematicOneDeleteconfirm(){
    
        $this->V(['thematic_id'=>['egNum',null,true]]);
        $id = intval($_POST['thematic_id']);
         
        $thematic = $this->table('thematic')->where(['id'=>$id,'is_on'=>1])->get(['id'],true);
    
        if(!$thematic){
            $this->R('',70009);
        }
        //删除图片文件
        $pic_url = $this->table('thematic')->where(['id'=>$id,'is_on'=>1])->get(['poster'],true);
        foreach ($pic_url as $key => $v) {
             $delete = unlink("../html".$v);
         }
        if (!$delete) {
            $this->R('',40020);
        }

        $thematic = $this->table('thematic')->where(['id'=>$id])->delete();
        if(!$thematic){
            $this->R('',40001);
        }

        $goods = $this->table('goods')->where(['thematic_id'=>$id,'is_on'=>1])->get(['id'],true);
        if ($goods) {
        $code = $this->table('goods')->where(['id'=>$id,'is_on'=>1])->delete();
        if(!$code){
            $this->R('',70009);
        }
        $code = $this->table('purchase')->where(['thematic_id'=>$id,'is_on'=>1])->delete();
        if(!$code){
            $this->R('',70009);
        }
        $code = $this->table('code')->where(['thematic_id'=>$id])->delete();
        if(!$code){
            $this->R('',70009);
        }
        }
         
        

        $this->R();
    }
}