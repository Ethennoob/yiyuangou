<?php
    namespace AppMain\controller\Admin;
    use \System\BaseClass;
    /**
     * 文章class
     */
    class ArticleController extends BaseClass {
        /**
         * 添加文章
         */
        public function articleAdd(){
            $rule = [
                'title'         =>[],
                'content'       =>[],
            ];
            $this->V($rule);

            foreach ($rule as $k=>$v){
                $data[$k] = $_POST[$k];
            }
            
           
            $data['add_time']     = time();
            $article = $this->table('article')->save($data);
            if(!$article){
                $this->R('',40001);
            }
            $this->R();
        }

        /**
         * 查询文章列表
         */
        public function articleList(){
            $where=['is_on'=>1];
            //$this->queryFilter，拼接查询字段
            $whereFilter=$this->queryFilter($where,['is_show'=>1]);
            $field=['id','title'];
            $pageInfo = $this->P();
            $class = $this->table('article')->where($whereFilter)->order('add_time desc');
            //查询并分页
            $articlelist = $this->getOnePageData($pageInfo,$class,'get','getListLength',[$field],false);
            if(!$articlelist){
                $articlelist = null;
            }
            //返回数据，参见System/BaseClass.class.php方法
            $this->R(['articlelist'=>$articlelist,'pageInfo'=>$pageInfo]);
        }

        /**
         * 查询一条文章信息
         */
        public function articleOneDetail(){
            $this->V(['article_id'=>['egNum',null,true]]);
            $id = intval($_POST['article_id']);
                //查询一条数据
                $article = $this->table('article')->where(['is_on'=>1,'id'=>$id])->get(null,true);
                if(!$article){
                    $this->R('',70009);
                }
                $article['update_time'] = $article['update_time'];
                $article['add_time'] = $article['add_time'];   
            $this->R(['article'=>$article]);
        }

        /**
         * 修改文章信息
         */
        public function articleEdit(){
            $rule = [
                'id'            =>['egNum',null,true],
                'title'         =>[null,null,true],
                'content'       =>[null,null,true],
            ];
            $this->V($rule);
            $id = intval($_POST['id']);
            $article = $this->table('article')->where(['id'=>$id,'is_on'=>1])->get(['id'],true);
            if(!$article){
                $this->R('',70009);
            }

            unset($rule['id']);
            foreach ($rule as $k=>$v){
                if(isset($_POST[$k])){
                    $data[$k] = $_POST[$k];
                }
            }
            
           
            $data['update_time']  = time();
        
            $article = $this->table('article')->where(['id'=>$id])->update($data);
            if(!$article){
                $this->R('',40001);
            }
            $this->R();
        }

        /**
         *删除一条或者文章数据（设置数据库字段为0，相当于回收站）
         */
        public function articleDelete(){
        
            $this->V(['id'=>['egNum',null,true]]);
            $id = intval($_POST['id']);
             
            $article = $this->table('article')->where(['id'=>$id,'is_on'=>1])->get(['id'],true);
        
            if(!$article){
                $this->R('',70009);
            }
        
            $article = $this->table('article')->where(['id'=>$id])->update(['is_on'=>0]);
            if(!$article){
                $this->R('',40001);
            }
            $this->R();
        }

        /**
         *删除一条或者多条文章数据（清除数据）
         */
        public function articleDeleteconfirm(){
        
            $this->V(['id'=>['egNum',null,true]]);
            $id = intval($_POST['id']);
             
            $article = $this->table('article')->where(['id'=>$id,'is_on'=>1])->get(['id'],true);
        
            if(!$article){
                $this->R('',70009);
            }
            
            $article = $this->table('article')->where(['id'=>$id])->delete();
            if(!$article){
                $this->R('',40001);
            }
            $this->R();
        }
        
    }