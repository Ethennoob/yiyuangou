<?php
    namespace AppMain\controller\Admin;
    use \System\BaseClass;
    /**
     * 微信类(自动回复功能和菜单功能)
     */
    class WechatController extends BaseClass {

    	/**
    	 * 自动回复列表
    	 */
    	public function autoReplyList(){
    		//$this->checkPriv('AutoReply', 1);

    		//分页
    		$pageInfo = $this->P();
    		
    		//筛选
    		if (isset($_POST['type'])&&in_array($_POST['type'], ['0','1','2'])){
    			$where['type']=$_POST['type'];
    		}

    		$where['is_on']=1;
        	$order='id desc';
        	$class = $this->table('wechat_response')->where($where)->order($order);
        	$field=['id','type','rsp_type','keywords','text','news','update_time','add_time'];
        	
        	$replyList = $this->getOnePageData($pageInfo, $class, 'get', 'getListLength', [$field], false);
        	if (!$replyList){
        		$replyList=null;
        	}
        	
        	$this->R(['replyList' => $replyList, 'pageInfo' => $pageInfo]);
    	}
    	
    	/**
    	 * 获取一条自动回复
    	 */
    	public function getAutoReply(){
    	    $rule=[
    	        'id'=>['egNum'],
    	    ];
    	     
    	    $this->V($rule);
    	    $field=['id','type','rsp_type','object','keywords','text','news'];
    	    $isReply=$this->table('wechat_response')->where(['id'=>$_POST['id']])->get($field,true);
    	    if (!$isReply){
    	        $this->R('','70002');
    	    }
    	    $this->R(['autoReply'=>$isReply]);
    	}
    	
    	/**
    	 * 添加自动回复
    	 */
    	public function addAutoReply(){
    		//$this->checkPriv('AutoReply', 2);
    		
    		$rule=[
    			'type'=>['in',['0','1','2']],
    			'rsp_type'=>['in',['0','1','2']],	
    			[
    				'keywords'=>[null,null,false],
    				'text'=>[null,null,false],
    				'news'=>['egNum',null,false]	
    			]
    		];
    		
    		$this->V($rule);
    		
    		$data=[
    		      'type'=>$_POST['type'],
    		      'rsp_type'=>$_POST['rsp_type'],
    		      'add_time'=>time()
    		];
    		
    		if ($_POST['type']==0){
    		    if (empty($_POST['keyword'])){
    		        $this->R('','40009');
    		    }
    		    $data['keywords']=$_POST['keyword'];
    		}
    		
    		if (isset($_POST['text'])){
    		    $data['text']=$_POST['text'];
    		}
    		if (isset($_POST['news'])){
    		    $data['news']=$_POST['news'];
    		}
    		
    		$rt=$this->table('wechat_response')->save($data);
    		
    		if (!$rt){
    			$this->R('','40001');
    		}
    		$this->R();
    	}
    	
    	/**
    	 * 编辑自动回复
    	 */
    	public function editAutoReply(){
    		//$this->checkPriv('AutoReply', 3);
    		
    		$rule=[
    				'id'=>['egNum'],
    				'rsp_type'=>['in',['0','1','2']],
    				[
    						'keywords'=>[null,null,false],
    						'text'=>[null,null,false],
    						'news'=>['egNum',null,false]
    				]
    		];
    	
    		$this->V($rule);
    		
    		$isReply=$this->table('wechat_response')->where(['id'=>$_POST['id']])->get(null,true);
    		if (!$isReply){
    			$this->R('','70002');
    		}
    		
    		$data=[
    		    'rsp_type'=>$_POST['rsp_type'],
    		    'update_time'=>time()
    		];
    		
    	   if ($_POST['type']==0){
    		    if (empty($_POST['keyword'])){
    		        $this->R('','40009');
    		    }
    		    $data['keywords']=$_POST['keyword'];
    		}
    		if (isset($_POST['text'])){
    		    $data['text']=$_POST['text'];
    		}
    		if (isset($_POST['news'])){
    		    $data['news']=$_POST['news'];
    		}
    		
    		$rt=$this->table('wechat_response')->where(['id'=>$_POST['id']])->update($data);
    	
    		if (!$rt){
    			$this->R('','40001');
    		}
    		$this->R();
    	}
    	
    	/**
    	 * 删除自动回复
    	 */
    	public function deleteAutoReply(){

    	    //$this->checkPriv('AutoReply', 4);
    	    
    	    $rule=[
    	        'id'=>['egNum'],
    	    ];
    	    
    	    $this->V($rule);
    	    $isReply=$this->table('wechat_response')->where(['id'=>$_POST['id']])->get(null,true);
    	    if (!$isReply){
    	        $this->R('','70002');
    	    }
    	    
    	    $data=[
    	        'is_on'=>0,
    	        'update_time'=>time()
    	    ];
    	    
    	    $rt=$this->table('wechat_response')->where(['id'=>$_POST['id']])->update($data);
    	    
    	    if (!$rt){
    	        $this->R('','40001');
    	    }
    	    $this->R();
    	}
         public function deleteConfirmAutoReply(){
        
            $this->V(['id'=>['egNum']]);
             
            $isReply=$this->table('wechat_response')->where(['id'=>$_POST['id']])->get(null,true);
            if (!$isReply){
                $this->R('','70002');
            }

            $isReply = $this->table('wechat_response')->where(['id'=>$_POST['id']])->delete();
            if(!$isReply){
                $this->R('',40001);
            }
            $this->R();
        }
    	
    	
        /**
         * 自定义菜单列表
         */
        public function menuList() {
        	//$this->checkPriv('Menu', 1);
        	
        	$list=$this->table('wechat_menu')->where(['is_on'=>1])->order("pid asc, sort asc")->get(['id','url','keyword','title','pid','sort']);
        	//dump($list);
        	// 取一级菜单
        	
        	if ($list){
        		foreach ( $list as $k => $vo ) {
        			if ($vo ['pid'] != 0)
        				continue;
        		
        			$vo['is_child']=0;
        			$one_arr[$vo ['id']] = $vo;
        			unset ( $list [$k] );
        		}
        		
        		foreach ( $one_arr as $k=>$p ) {
        			$data [] = $p;
        			$two_arr = array ();
        			foreach ( $list as $key => $l ) {
        				if ($l ['pid'] != $p ['id'])
        					continue;
        		
        				$l ['title'] = $l ['title'];
        				$two_arr [] = $l;
        				$one_arr[$k]['is_child']=1;
        				unset($one_arr[$k]['keyword']);
        				unset($one_arr[$k]['url']);
        				unset ( $list [$key] );
        			}
        			if ($two_arr){
        				$one_arr[$k]['child']=$two_arr;
        			}
        		}
        		$one_arr=array_values($one_arr);
        	}
        	else{
        		$one_arr=null;
        	}
        	//dump($one_arr);
        	$this->R(['menuList' => $one_arr]);		
        }
    	
        /**
         * 修改自定义菜单
         */
        public function editMenu(){
            //$this->checkPriv('Menu', 3);
            
            $rule=[
                'list'=>[]
            ];
            $this->V($rule);
            
            if (count($_POST['list']) > 3){
                $this->R('','40018');
            }
            //dump($_POST['list']);exit;
            $this->table('wechat_menu')->startTrans();
            
            $delete=$this->table('wechat_menu')->where(['is_on'=>1])->update(['is_on'=>0]);
            if (!$delete){
                $this->table('wechat_menu')->rollback();
                $this->R('','40001');
            }
            
            $list=$_POST['list'];
            foreach ($list as $a){
                $parentData=[
                        'pid'=>0,
                        'keyword'=>!empty($a['keyword'])?$a['keyword']:'',
                        'url'=>$a['url'],
                        'sort'=>$a['sort'],
                        'title'=>$a['title'],
                        'add_time'=>time()
                ];
                
                $add=$this->table('wechat_menu')->save($parentData);
                if (!$add){
                    $this->table('wechat_menu')->rollback();
                    $this->R('','40001');
                }
                
                if ($a['is_child']){
                    if (count($a['is_child']) > 5){
                        $this->table('wechat_menu')->rollback();
                        $this->R('','40018');
                    }
                    
                    foreach ($a['child'] as $key=>$b){
                        $childData=[
                            'pid'=>$add,
                            'title'=>$b['title'],
                            'keyword'=>$b['keyword'],
                            'url'=>$b['url'],
                            'sort'=>$key+1,
                            'add_time'=>time()
                        ];
                        
                        $addChild=$this->table('wechat_menu')->save($childData);
                        if (!$addChild){
                            $this->table('wechat_menu')->rollback();
                            $this->R('','40001');
                        }
                    }
                }
            }
                    
            $this->table('wechat_menu')->commit();
            $this->R();
        }  
        
        /**
         * 同步/创建自定义菜单
         */
        public function sysMenu(){
        	//$this->checkPriv('Menu', 5);

        	$data = $this->table('wechat_menu')->where(['is_on'=>1])->order("pid asc, sort asc")->get();
        	foreach ( $data as $k => $d ) {
        		if ($d ['pid'] != 0)
        			continue;
        		$tree ['button'] [$d ['id']] = $this->_deal_data ( $d );
        		unset ( $data [$k] );
        	}
        	foreach ( $data as $k => $d ) {
        		$tree ['button'] [$d ['pid']] ['sub_button'] [] = $this->_deal_data ( $d );
        		unset ( $data [$k] );
        	}
        	$tree2 = array ();
        	$tree2 ['button'] = array ();
        
        	foreach ( $tree ['button'] as $k => $d ) {
        		$tree2 ['button'] [] = $d;
        	}
        	 
        	$weObj = new \System\lib\Wechat\Wechat($this->config('WEIXIN_CONFIG'));
        	$send=$weObj->createMenu($tree2);
        	 
        	if ($send){
        		$this->R();
        	}
        	else{
        		$this->R(['errorMsg'=>"菜单创建失败！失败信息:".$weObj->errMsg." 错误码：".$weObj->errCode],'');
        	}
        }
        
        /**
         * 自定义菜单处理数据
         * @param array $d
         * @return array
         */
        function _deal_data($d) {
        	$res ['name'] = $d ['title'];
        
        	if (! empty ( $d ['keyword'] )) {
        		$res ['type'] = 'click';
        		 
        		$res ['key'] = $d ['keyword'];
        	} else {
        		$res ['type'] = 'view';
        		$res ['url'] = $d ['url'];
        	}
        	return $res;
        }
        function json_encode_cn($data) {
        	$data = json_encode ( $data );
        	return preg_replace ( "/\\\u([0-9a-f]{4})/ie", "iconv('UCS-2BE', 'UTF-8', pack('H*', '$1'));", $data );
        }

    }
