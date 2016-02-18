<?php
/**
 * 反向团购系统---开团列表管理类
 * @authors 凌翔 (553299576@qq.com)
 * @date    2016-01-07 15:57:59
 * @version $Id$
 */

namespace AppMain\controller\Groupbuy;
use \System\BaseClass;

class BackendgroupsController extends BaseClass {
    /**
     * 开团列表
     */
    public function groupList(){
    	$pageInfo = $this->P();
        //调用Helper类
        $dataClass=$this->H('GroupList');
        if (isset($_POST['goods_id'])) {
            $this->V(['goods_id'=>['egNum']]);
            $goods_id = intval($_POST['goods_id']); 
            $where = 'A.is_on = 1 and A.goods_id='.$goods_id;
        }else{
            $where = 'A.is_on = 1';
        }
        $order='A.id desc';
        $groupList=$dataClass->getGroupList(null,null,null,false,$order);
        $groupList=$this->getOnePageData($pageInfo, $dataClass, 'getGroupList','getGroupListListLength',[$where,null,null,false,$order],true);
        if($groupList){
                foreach ($groupList as $k=>$v){
                    $status = $this->table('user')->where(['is_on'=>1,'id'=>$v['leader']])->get(['nickname','user_img'],true);
                    $groupList[$k]['leader_nickname'] = $status['nickname'];
                    $groupList[$k]['leader_user_img'] = $status['user_img'];
                    $status = $this->table('user')->where(['is_on'=>1,'id'=>$v['soft']])->get(['nickname','user_img'],true);
                    $groupList[$k]['soft_nickname'] = $status['nickname'];
                    $groupList[$k]['soft_user_img'] = $status['user_img'];
                    $status = $this->table('user')->where(['is_on'=>1,'id'=>$v['stool']])->get(['nickname','user_img'],true);
                    $groupList[$k]['stool_nickname'] = $status['nickname'];
                    $groupList[$k]['stool_user_img'] = $status['user_img'];
                    $status = $this->table('groupbuy_group_member')->where(['is_on'=>1,'group_id'=>$v['id']])->get(['user_id'],false);
                    $groupList[$k]['join_num'] = count($status);
                    foreach ($status as $key => $user) {
                    	$status = $this->table('user')->where(['is_on'=>1,'id'=>$user['user_id']])->get(['nickname','user_img','openid'],true);
                    	$count = count($status);
                    	$v = @implode(";",$status);
                    	$temp[] = $v;
                    }
                    $groupList[$k]['userList']=$temp;
                    unset($temp); 
                }
        }else{
            $groupList=null;
        }
        $this->R(['groupList'=>$groupList,'page'=>$pageInfo]);
    }
    
}