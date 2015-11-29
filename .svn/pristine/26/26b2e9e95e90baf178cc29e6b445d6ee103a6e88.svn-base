<?php
/**
 * Description of BaseView
 *
 * @author lzy
 */
namespace System;
abstract class BaseView {

    private $tpl; //模板辅助对象
    protected $vars; //所有需要输出到页面的变量，键值对
    private static $viewObj;
    public $pageInfo;

    public function __construct() {
        $this->vars = array();
        $this->pageInfo = new PageInfo();
        self::$viewObj = $this;
    }

    public static function getCurrViewObj() {
        return self::$viewObj;
    }

    abstract public function action();

    public function __set($key, $value) {
        $this->vars[$key] = $value;
    }

    public function __get($key) {
        return $this->vars[$key];
    }

    public function echo_error($string) {
        header("Content-type: text/html; charset=utf-8");
        echo $string;
    }

    //如需要使用模板，则可调用此方法来初始化模板辅助对象
    public function initTpl($tplPath, $tplCompilePath) {
        $this->tpl = new PHPTplHelper();
        $this->tpl->setTPLPath($tplPath); //模板所在路径
        $this->tpl->setTPLCompilePath($tplCompilePath); //模板编译后保存路径
    }

    //如需要使用模板，则可调用此方法来给模板变量赋值
    public function assignTpl() {
        if ($this->tpl) {
            $this->tpl->assignMult($this->vars);
            $this->tpl->assignWholeObject("pi", $this->pageInfo);
            $this->tpl->assign("DOMAIN", "http://" . _DOMAIN_);
        }
    }

    //如需要使用模板，则可调用此方法来应用模板
    public function applyTpl($tplFileName) {
        if ($this->tpl) {
            $this->tpl->setTPLFileName($tplFileName); //模板文件名
            $this->tpl->display();
        }
    }

    public function useTpl($tplPath, $tplCompilePath, $tplFileName) {
        $this->initTpl($tplPath, $tplCompilePath);
        $this->assignTpl();
        $this->applyTpl($tplFileName);
    }

}

?>
