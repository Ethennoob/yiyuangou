<?php
/**
 * 一元购系统---系统设置类
 * @authors 凌翔 (553299576@qq.com)
 * @date    2015-11-28 15:55:01
 * @version $Id$
 */

namespace AppMain\controller\Admin;
use \System\BaseClass;

class SystemController extends BaseClass {
    /**
     * 设置认购上限(百分数)
     */
    public function buyLimitEdit()
        {  
            $rule = [
                    'buy_limit'      =>['egNum'],
                ];
                $this->V($rule); 

                foreach ($rule as $k => $v) {
                        $data[$k] = $_POST[$k];
                }

            $data['update_time'] = time();
           

                $goods = $this->table('system')->where(['id'=>1])->update($data);
                if(!$goods){
                    $this->R('',40001);
                }
            $this->R(); 
        }
    /**
     *查询设置认购上限(百分数)
     */
    public function buyLimitDetail(){
        $buy_limit = $this->table('system')->where(['id'=>1])->get(['buy_limit'],true);
        if(!$buy_limit){
            $this->R('',70009);
        }
     $this->R(['buy_limit'=>$buy_limit]);

    }
    /**
     *查询设置B值
     */
    public function Bvalue(){
        $Bvalue = $this->table('system')->where(['id'=>1])->get(['Bvalue'],true);
        if(!$Bvalue){
            $this->R('',70009);
        }
     $this->R(['Bvalue'=>$Bvalue['Bvalue']]);

    }
}