<?php
/**
 * 反向团购系统---团类
 * @authors 凌翔 (553299576@qq.com)
 * @date    2016-01-09 13:21:24
 * @version $Id$
 */

namespace AppMain\controller\Groupbuy;
use \System\BaseClass;

class GroupsController extends Baseclass {
    
    /**
     * 我的团
     */
    public function myGroups(){
    	$this->V(['user_id'=>['egNum',null,true]]);
        $id = intval($_POST['user_id']);
        $pageInfo = $this->P();
        //调用Helper类
        $dataClass=$this->H('Groups');
        $where = 'A.is_on = 1 and A.user_id='.$id;
        $order='A.add_time desc';
        $myGroups=$dataClass->getMyGroups(null,null,null,false,$order);
        $myGroups=$this->getOnePageData($pageInfo, $dataClass, 'getMyGroups','getMyGroupsListLength',[$where,null,null,false,$order],true);
        if($myGroups){
            foreach ($myGroups as $k => $v) {
            $status = $this->table('groupbuy_bill')->where(['is_on'=>1,'group_id'=>$v['group_id'],'user_id'=>$id])->get(['id'],true);
            $myGroups[$k]['bill_id']=$status['id'];
            $good = $this->table('groupbuy_goods')->where(['is_on'=>1,'id'=>$v['goods_id']])->get(['goods_album'],true);
            $ImgUrl=explode(';', $good['goods_album']);
            $myGroups[$k]['goods_img'] = $ImgUrl[0];
            unset($myGroups[$k]['goods_album'] );
            $myGroups[$k]['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
            }
        }else{
            $myGroups=null;
        }
        $this->R(['myGroups'=>$myGroups,'page'=>$pageInfo]);
    }
    /**
     * 开团详情
     */
    public function groupOneDetail(){
    	$this->V(['group_id'=>['egNum',null,true]]);
        $id = intval($_POST['group_id']);
        $pageInfo = $this->P();
        //调用Helper类
        $dataClass=$this->H('Groups');
        $where = 'A.is_on = 1 and A.id='.$id;
        $order='A.id desc';
        $groupOneDetail=$dataClass->getGroupsDetail(null,null,null,true,$order);
        $good = $this->table('groupbuy_goods')->where(['id'=>$groupOneDetail['goods_id']])->get(['goods_album'],true);
        $ImgUrl=explode(';', $good['goods_album']);
        $groupOneDetail['goods_img'] = $ImgUrl[0];
        unset($groupOneDetail['goods_album']);
        $groupOneDetail['end_time'] = $groupOneDetail['add_time']+$groupOneDetail['group_time']*3600;
        //开团用户列表
        $pageInfo = $this->P();
        $file = ['id','user_id','add_time'];
        $class = $this->table('groupbuy_group_member')->where(['is_on'=>1,'group_id'=>$id])->order('id desc');
        //查询并分页
        $userList = $this->getOnePageData($pageInfo,$class,'get','getListLength',[$file],false);
        if ($userList) {
        foreach ($userList as $k => $v) {
        	$status = $this->table('user')->where(['is_on'=>1,'id'=>$v['user_id']])->get(['user_img'],true);
            $userList[$k]['user_img'] = $status['user_img'];
            $userList[$k]['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
        }
        $groupOneDetail['last_num'] = $groupOneDetail['num']-count($userList);
        }else{
            $userList=null;
        }
        
        $this->R(['groupOneDetail'=>$groupOneDetail,'userList'=>$userList,'page'=>$pageInfo]);
    }
}