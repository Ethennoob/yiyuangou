<?php

namespace AppMain\controller\Admin;
use \System\BaseClass;
    /**
     * 专区类
     */
class CompanyController extends BaseClass {
    /**
     * 增加专区
     */
    public function companyOneAdd(){

        $rule = ['company_name' =>[]];
        $this->V($rule);
        
        foreach ($rule as $k=>$v){
            $data[$k] = $_POST[$k];
        }
        $data["add_time"]    = time();
        $data["update_time"] = time();
        $company = $this->table('company')->save($data);
        if(!$company){
            $this->R('',40001);
            }else{
                $company = $this->table('company')->where(['is_on'=>1])->order('add_time desc')->limit(0,1)->get(['id'],false);
                if (!$company) {
                    $this->R('',40001);
                }
                $this->setQrcode($company[0]['id']);
            }
        
        $this->R();
        }
        /**
         * 生成二维码
         */
        private function setQrcode($qrcode_id){
        $id = $qrcode_id;
        //查询一条数据
        $company = $this->table('company')->where(['is_on'=>1,'id'=>$id])->get(null,true);
        if(!$company){
            $this->R('',70009);
        }
        //$url = "http://onebuy.ping-qu.com/index.html/company_id=".$id;
        $url = "http://onebuy.ping-qu.com/index.html?company_id=38";
        $this->vendor('Phpqrcode.phpqrcode#class');
        $qr = new \QRcode();
        $qr->width=360;
        $time =time();
        $QRcode=$qr->png($url,false,QR_ECLEVEL_H,10,4,false,$time);
        $data['update_time']  = time();
        $data['QR_code'] = "../images/company/".$time.".png";
        $company = $this->table('company')->where(['id'=>$id])->update($data);
        if(!$company){
            $this->R('',40001);
        }
        $this->R();
    }
    /**
     * 轮播图列表
     */
    public function companyOneList(){
        $where=['is_on'=>1];
        $pageInfo = $this->P();
        $class = $this->table('company')->where($where)->order('add_time desc');
        //查询并分页
        $company = $this->getOnePageData($pageInfo,$class,'get','getListLength',null,false);
        if($company){
            foreach ($company as $k=>$v){
                $company[$k]['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
                $company[$k]['update_time'] = date('Y-m-d H:i:s',$v['update_time']);
            }
        }else{
            $company = null;
        }
        //返回数据，参见System/BaseClass.class.php方法
        $this->R(['company'=>$company,'pageInfo'=>$pageInfo]);
    }
    /**
     * 查询一条数据
     */
    public function companyOneDetail(){
        $this->V(['company_id'=>['egNum']]);
        $id = intval($_POST['company_id']); 
        // //查找memcache缓存

            //查询一条数据
            $company = $this->table('company')->where(['is_on'=>1,'id'=>$id])->get(null,true);
            if(!$company){
                $this->R('',70009);
            }
            $company['update_time'] = date('Y-m-d H:i:s',$company['update_time']);
            $company['add_time'] = date('Y-m-d H:i:s',$company['add_time']);        
         $this->R(['company'=>$company]);
    }
    /**
     * 修改一条轮播图数据
     */
    public function companyOneEdit(){
        $rule = [
            'company_id'         =>['egNum'],
            'company_name'   =>[],
            //'QR_code'    =>[],
        ];
        $this->V($rule);
        $id = intval($_POST['company_id']);
        $company = $this->table('company')->where(['id'=>$id,'is_on'=>1])->get(['id'],true);
        if(!$company){
            $this->R('',70009);
        }
        unset($rule['id']);
        foreach ($rule as $k=>$v){
            if(isset($_POST[$k])){
                $data[$k] = $_POST[$k];
            }
        }
        $data['update_time']  = time();
        $company = $this->table('company')->where(['id'=>$id])->update($data);
        if(!$company){
            $this->R('',40001);
        }
        $this->R();
    }
    /**
     *删除一条文章数据（设置数据库字段为0）
     */
    public function companyOneDelete(){
    
        $this->V(['company_id'=>['egNum',null,true]]);
        $id = intval($_POST['company_id']);
         
        $company = $this->table('company')->where(['id'=>$id,'is_on'=>1])->get(['id'],true);
    
        if(!$company){
            $this->R('',70009);
        }
    
        $company = $this->table('company')->where(['id'=>$id])->update(['is_on'=>0]);
        if(!$company){
            $this->R('',40001);
        }
        $this->R();
    }
    /**
     * 删除一条轮播图数据
     */
    public function companyOneDeleteconfirm(){
    
        $this->V(['company_id'=>['egNum']]);
        $id = intval($_POST['company_id']);
         
        $company = $this->table('company')->where(['id'=>$id,'is_on'=>1])->get(['id'],true);
    
        if(!$company){
            $this->R('',70009);
        }
        //删除图片文件
        $adv_url = $this->table('company')->where(['id'=>$id,'is_on'=>1])->get(['QR_code'],true);
        foreach ($adv_url as $k => $v) {
             $delete = @unlink("../html".$v);
         }
        if (!$delete) {
            $this->R('',40020);
        }

        $company = $this->table('company')->where(['id'=>$id])->delete();
        if(!$company){
            $this->R('',40001);
        }
        
        $this->R();
    }
}
