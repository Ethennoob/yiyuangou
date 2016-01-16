<?php
namespace AppMain\controller\Admin;
use \System\BaseClass;
    /**
     *   轮播图功能
     */

class AdvertisementController extends BaseClass {
    /**
     * 增加轮播图
     */
    public function test1(){
        echo strtotime("2016-01-14")."</br>";
        echo strtotime("2016-01-15");

        echo date("Y-m-d",time());
        echo date("Y-m-d",1452700800);
        echo date("Y-m-d",1452756660);
        echo date("Y-m-d",1452700000);
 
    }
    public function advertisementOneAdd(){

        $rule = [
            'adv_name'        =>[],
            'adv_url'         =>[],
            'company_id'      =>['egNum'],
        ];
        
         $this->V($rule);
        
        foreach ($rule as $k=>$v){
            $data[$k] = $_POST[$k];
        }
        $imgArray=$this->H('PictureUpload')->pictureUpload($_FILES['adv_img'],'advertisement',true);
        if (!$imgArray) {
            $errorMsg=$imgArray->getError();
            $this->R(['errorMsg'=>$errorMsg],'40019');
        }
        $data["add_time"]    = time();
        $data["update_time"] = time();
        $data['adv_img']  = $imgArray['img'];
        $data['adv_img_thumb'] = $imgArray['thumb'];
        $advertisement = $this->table('advertisement')->save($data);
            if(!$advertisement){
                $this->R('',40001);
                }
        $this->R();
           }
    /**
     * 轮播图列表
     */
    public function advertisementOneList(){
        $this->V(['company_id'=>['egNum']]);
        $id = intval($_POST['company_id']); 
        $where=['is_on'=>1,'company_id'=>$id];
        $pageInfo = $this->P();
        $class = $this->table('advertisement')->where($where)->order('update_time desc');
        //查询并分页
        $advertisement = $this->getOnePageData($pageInfo,$class,'get','getListLength',null,false);
        if($advertisement){
            foreach ($advertisement as $k=>$v){
                $advertisement[$k]['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
                $advertisement[$k]['update_time'] = date('Y-m-d H:i:s',$v['update_time']);
                $status = $this->table('company')->where(['is_on'=>1,'id'=>$id])->get(['company_name'],true);
            $advertisement[$k]['company_name'] = $status['company_name'];
            }
        }else{
            $advertisement = null;
        }
        //返回数据，参见System/BaseClass.class.php方法
        $this->R(['advertisement'=>$advertisement,'pageInfo'=>$pageInfo]);
    }
    /**
     * 查询一条数据
     */
    public function advertisementOneDetail(){
        $this->V(['id'=>['egNum']]);
        $id = intval($_POST['id']); 
        // //查找memcache缓存
         
            //查询一条数据
            $advertisement = $this->table('advertisement')->where(['is_on'=>1,'id'=>$id])->get(null,true);
            if(!$advertisement){
                $this->R('',70009);
            }
            $advertisement['update_time'] = date('Y-m-d H:i:s',$advertisement['update_time']);
            $advertisement['add_time'] = date('Y-m-d H:i:s',$advertisement['add_time']);
            $status = $this->table('company')->where(['is_on'=>1,'id'=>$id])->get(['company_name'],true);
            $advertisement['company_name'] = $status['company_name'];
           
        
         $this->R(['advertisement'=>$advertisement]);
    }
        public function test(){
        $test = $this->S()->set('test',"hello",60*60);
        if ($test) {
            $test1=$this->S()->get('test');
            print_r($test1);
        }else{
            echo "error!";
        }
    }
    /**
     * 修改一条轮播图数据
     */
    public function advertisementOneEdit(){
        $rule = [
            'advertisement_id'         =>['egNum'],
            'adv_name'   =>[],
            'adv_url'    =>[],
            'company_id' =>['egNum'],
        ];
        $this->V($rule);
        $id = intval($_POST['advertisement_id']);
        $advertisement = $this->table('advertisement')->where(['id'=>$id,'is_on'=>1])->get(['id'],true);
        if(!$advertisement){
            $this->R('',70009);
        }
        foreach ($advertisement as $k => $v) {
             $delete = @unlink($v);
         }
        unset($rule['id']);
        foreach ($rule as $k=>$v){
            if(isset($_POST[$k])){
                $data[$k] = $_POST[$k];
            }
        }
        if (isset($_FILES['adv_img'])) {
            $imgArray=$this->H('PictureUpload')->pictureUpload($_FILES['adv_img'],'advertisement',true);
            if (!$imgArray) {
                $errorMsg=$imgArray->getError();
                $this->R(['errorMsg'=>$errorMsg],'40019');
            }
            $data['adv_img']  = $imgArray['img'];
            $data['adv_img_thumb'] = $imgArray['thumb'];
        }
        $data['update_time']  = time();
        $advertisement = $this->table('advertisement')->where(['id'=>$id])->update($data);
        if(!$advertisement){
            $this->R('',40001);
        }
        //活动更改了内容，删除活动信息的memcache,缓存
        $this->S()->delete('advertisement_'.$id);
        $this->R();
    }
    /**
     *删除一条文章数据（设置数据库字段为0）
     */
    public function advertisementOneDelete(){
    
        $this->V(['id'=>['egNum',null,true]]);
        $id = intval($_POST['id']);
         
        $advertisement = $this->table('advertisement')->where(['id'=>$id,'is_on'=>1])->get(['id'],true);
    
        if(!$advertisement){
            $this->R('',70009);
        }
    
        $advertisement = $this->table('advertisement')->where(['id'=>$id])->update(['is_on'=>0]);
        if(!$advertisement){
            $this->R('',40001);
        }
        $this->S()->delete('advertisement_'.$id);
        $this->R();
    }
    /**
     * 删除一条轮播图数据
     */
    public function advertisementOneDeleteconfirm(){
    
        $this->V(['id'=>['egNum']]);
        $id = intval($_POST['id']);
         
        $advertisement = $this->table('advertisement')->where(['id'=>$id,'is_on'=>1])->get(['id'],true);
    
        if(!$advertisement){
            $this->R('',70009);
        }
        //删除图片文件
        $adv_url = $this->table('advertisement')->where(['id'=>$id,'is_on'=>1])->get(['adv_img'],true);
        foreach ($adv_url as $k => $v) {
             $delete = unlink("../html".$v);
         }
        if (!$delete) {
            $this->R('',40020);
        }

        $advertisement = $this->table('advertisement')->where(['id'=>$id])->delete();
        if(!$advertisement){
            $this->R('',40001);
        }
        
        $this->S()->delete('advertisement_'.$id);
        $this->R();
    }


}