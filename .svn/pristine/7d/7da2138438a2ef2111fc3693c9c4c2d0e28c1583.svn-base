<?php
    namespace AppMain\controller\Admin;
    use \System\BaseClass;
    class NewsController extends BaseClass {
        /**
         * 微信图文素材class
         */
        public function newsList() {
        	//$this->checkPriv('News', 1);
        	
            $pageInfo = $this->P();
            $class = $this->H('News');
            $where=['is_on'=>1];
            $field=['id','media_id','title','author','img','img_thumb_360','url','desc','sort','add_time'];
            $order='id desc';
            $newsList = $this->getOnePageData($pageInfo, $class, 'getNewsList', 'getNewsListLength', [$where,$field,$order], true);

            if (!$newsList){
            	$newsList=null;
            }

            $this->R(['newsList' => $newsList, 'pageInfo' => $pageInfo]);
        }
        
        /**
         * 获取一条图文素材
         */
        public function news(){
        	//$this->checkPriv('News', 1);
        	$this->V(['media_id'=>[]]);
        	
        	$field=['id','title','author','content','img','img_thumb_360','img_media_id','url','desc','media_id','sort'];
        	$news=$this->table('wechat_news')->where(['media_id'=>$_POST['media_id']])->order('sort ASC')->get($field);
        	
        	if ($news){
        		foreach ($news as $key=>$a){
        			$news[$key]['img']=$this->config('IMG_PATH').$a['img'];
        			$news[$key]['img_thumb_360']=$this->config('IMG_PATH').$a['img_thumb_360'];
        		}
        		
        	}
        	else{
        		$this->R('','70007');
        	}
        	$this->R(['news'=>$news]);
        }
        
        /**
         * 添加图文素材
         */
        public function addNews(){
        	//$this->checkPriv('News', 2);
        	$rule=[
        		'count'=>['egNum',8],
        		'list' =>[],
        	];
        	
       		$this->V($rule);
        	$data=$_POST;
        	
        	foreach ($data['list'] as $key=>$a){
        		$rule=[
        			'title'=>[],
        			'content'=>[],	
        			'img_media_id'=>[],	
        		];
        		$this->V($rule,$a);
        		
        		if (!empty($a['url'])){
        			$this->V(['url'=>['url']]);
        		}
        		
        	}
        	
        	$this->table('wechat_news')->startTrans();
        	$media_id=time().rand(10, 99);
        	foreach ($data['list'] as $key=>$a){
        		$isImgMedia=$this->table('media')->where(['id'=>$a['img_media_id'],'is_on'=>1])->get(null,true);
        		if (!$isImgMedia){
        			$this->table('wechat_news')->rollback();
        			$this->R('','40011');
        		}
        		
        		$saveData=[
        			'title'=>$a['title'],
        			'author'=>$a['author'],
        			'desc' => $a['desc'],	
        			'content'=>$a['content'],	
        			'url'=>$a['url'],	
        			'img_media_id'=>$a['img_media_id'],	
        			'img'=>$isImgMedia['path'],
        			'img_w_h'=>$isImgMedia['w_h'],
        			'img_thumb_360'=>$isImgMedia['thumb_360_path'],
        			'img_thumb_360_w_h'=>$isImgMedia['thumb_360_w_h'],
        			'media_id'=>$media_id,	
        			'sort'=>$key,	
        			'add_time'=>time()
        		];
        		
        		$rt=$this->table('wechat_news')->save($saveData);
        		if (!$rt){
        			$this->table('wechat_news')->rollback();
        			$this->R('','40001');
        		}
        	}
        	
        	$this->table('wechat_news')->commit();
        	$this->R();
        }
        
        /**
         * 编辑图文素材
         */
        public function editNews(){
        	//$this->checkPriv('News', 3);
        	$rule=[
        		'media_id'=>[],
        		'count'=>['egNum',8],
        		'list' =>[],
        		//['list'=>['egNum',count(@$_POST['list'])]]
        	];
        	
        	$this->V($rule);
        	$data=$_POST;
        	
        	foreach ($data['list'] as $key=>$a){
        		$rule=[
        			'title'=>[],
        			'content'=>[],	
        			'img_media_id'=>[],	
        		];
        		$this->V($rule,$a);
        		
        		if (!empty($a['url'])){
        			$this->V(['url'=>['url']]);
        		}
        		
        	}
        	
        	$this->table('wechat_news')->startTrans();
        	$media_id=$_POST['media_id'];
        	
        	$delete=$this->table('wechat_news')->where(['media_id'=>$media_id])->delete();
        	if (!$delete){
        		$this->table('wechat_news')->rollback();
        		$this->R('','40001');
        	}
        	
        	foreach ($data['list'] as $key=>$a){
        		$isImgMedia=$this->table('media')->where(['id'=>$a['img_media_id']])->get(null,true);
        		if (!$isImgMedia){
        			$this->table('wechat_news')->rollback();
        			$this->R('','40011');
        		}
        		
        		$saveData=[
        			'title'=>$a['title'],
        			'author'=>$a['author'],
        			'desc' => $a['desc'],	
        			'content'=>$a['content'],	
        			'url'=>$a['url'],	
        			'img_media_id'=>$a['img_media_id'],	
        			'img'=>$isImgMedia['path'],
        			'img_w_h'=>$isImgMedia['w_h'],
        			'img_thumb_360'=>$isImgMedia['thumb_360_path'],
        			'img_thumb_360_w_h'=>$isImgMedia['thumb_360_w_h'],
        			'media_id'=>$media_id,	
        			'sort'=>$key,	
        			'add_time'=>time()
        		];
        		
        		$rt=$this->table('wechat_news')->save($saveData);
        		if (!$rt){
        			$this->table('wechat_news')->rollback();
        			$this->R('','40001');
        		}
        	}
        	
        	$this->table('wechat_news')->commit();
        	$this->R();
        	
        	
        }
       
        /**
         * 删除图文素材
         */
        public function deleteNews(){
        	//$this->checkPriv('News', 3);
        	
        	$rule=[
        			'media_id'=>[],
        	];
        	$this->V($rule);
        	$data=$_POST;
        	$isNews=$this->table('wechat_news')->where(['media_id'=>$data['media_id'],'is_on'=>1])->get();
        	if (!$isNews){
        		$this->R('','40011');
        	}
        	
        	$news=$this->table('wechat_news')->where(['media_id'=>$data['media_id']])->update(['is_on'=>0]);
        	//echo $this->table('wechat_news')->getLastSql();
        	if(!$news){
        		$this->R('','40001');
        	}
        	$this->R();
        }
        
        /**
         * 文章列表
         */
        public function articleList(){
        	//$this->checkPriv('Article', 1);
        	
        	$pageInfo = $this->P();
        	$where=['is_on'=>1];
        	$order='id desc';
        	$class = $this->table('article')->where($where)->order($order);
        	$field=['id','title','author','is_system','update_time','add_time'];
        	
        	$articleList = $this->getOnePageData($pageInfo, $class, 'get', 'getListLength', [$field], false);
        	if (!$articleList){
        		$articleList=null;
        	}
        	
        	$this->R(['articleList' => $articleList, 'pageInfo' => $pageInfo]);
        }


    }
