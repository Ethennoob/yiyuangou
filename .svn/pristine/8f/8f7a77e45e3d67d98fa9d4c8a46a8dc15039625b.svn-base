<?php
/**
 * 一元购系统---专题管理类
 * @authors 凌翔 (553299576@qq.com)
 * @date    2015-11-25 22:04:46
 * @version $Id$
 */

namespace AppMain\controller\Admin;
use \System\BaseClass;

class thematicController extends BaseClass {
    
    /**
    *添加专题
    *专题编号，专题名称，专题海报
     */
    public function thematicOneAdd(){
        //-----------字段验证-----------
        $rule = [
            'thematic_name'   =>[],
            'nature'          =>['in',[0,1]],
            //'poster'          =>[],
        ];
        $this->V($rule);
        
        foreach ($rule as $k=>$v){
            $data[$k] = $_POST[$k];
        }
         //图片上传
        $pictureName = $_FILES['poster'];
        $imgarray = $this->H('PictureUpload')->pictureUpload($pictureName,'poster',false);

        $data['add_time']     = time();
        $data['update_time']     = time();
        $data['poster'] = $imgarray['img'];
    
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
            'id'              =>['egNum'],//已有会员id
            'thematic_name'   =>[],
            'nature'          =>['in',[0,1]],
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
        $pictureName = $_FILES['poster'];
        $imgarray = $this->H('PictureUpload')->pictureUpload($pictureName,'poster',false);
        //删除图片文件
        $pic_url = $this->table('thematic')->where(['id'=>$id,'is_on'=>1])->get(['poster'],true);
        foreach ($pic_url as $key => $v) {
             @unlink("../html".$v);
         }
       
        $data['poster'] = $imgarray['img'];

        $thematic = $this->table('thematic')->where(['id'=>$id])->update($data);
        if(!$thematic){
            $this->R('',40001);
        }

        $this->R();
    }
    /**
     * 查询专题列表(分页)
     */
    public function thematicList(){
        $pageInfo = $this->P();
        $file = ['id','thematic_name','nature','status','is_show','add_time'];

        $class = $this->table('thematic')->where(['is_on'=>1])->order('add_time desc');

        //查询并分页
        $thematicpage = $this->getOnePageData($pageInfo,$class,'get','getListLength',[$file],false);
        if($thematicpage ){
            foreach ($thematicpage  as $k=>$v){
                $thematicpage [$k]['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
                $goodsarr = $this->table('goods')->where(['is_on'=>1,'thematic_id'=>$v['id']])->get(['goods_name','price','limit_num'],false);
                $count = count($goodsarr);
                    for ($i=0; $i < $count; $i++) { 
                        $thematicpage [$k]['goods_'.$i] = implode(";", $goodsarr[$i]);
                    }
            }
        }else{
            $thematicpage  = null;
        }
        //返回数据，参见System/BaseClass.class.php方法
        $this->R(['thematicpage'=>$thematicpage,'pageInfo'=>$pageInfo]);
        
    }
    /**
     * 查询一条专题信息
     */
    public function thematicOneDetail(){
        $this->V(['id'=>['egNum',null,true]]);
        $id = intval($_POST['id']);
        $thematic = $this->table('thematic')->where(['id'=>$id,'is_on'=>1])->get(['id'],true);
        if(!$thematic){
            $this->R('',70009);
        }
        $thematic = $this->table('thematic')->where(['id'=>$id,'is_on'=>1])->get(null,true);
        if(!$thematic){
            $this->R('',70009);
        }
        $this->R(['thematic'=>$thematic]);
    }
    /*
    删除一条专题信息数据（设置数据库字段为0，相当于回收站）
     */
    public function thematicOneDelete(){
    
        $this->V(['id'=>['egNum',null,true]]);
        $id = intval($_POST['id']);
         
        $thematic = $this->table('thematic')->where(['id'=>$id,'is_on'=>1])->get(['id'],true);
    
        if(!$thematic){
            $this->R('',70009);
        }
    
        $thematic = $this->table('thematic')->where(['id'=>$id])->update(['is_on'=>0]);
        if(!$thematic){
            $this->R('',40001);
        }

        $this->R();
    }
    /**
     *删除一条专题信息（清除数据）
     */
    public function thematicOneDeleteconfirm(){
    
        $this->V(['id'=>['egNum',null,true]]);
        $id = intval($_POST['id']);
         
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

        $this->R();
    }
}