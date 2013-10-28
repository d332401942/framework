<?php

class ViewCoreLib extends Feng {

    private $templateFile = null;
    private $compile = null;
    public $isDisplay = false;
    protected $isAjax = false;
    protected $currentUrl;

    public function __construct() {
        $this->compile = new CompileVendorLib();
        Main::$isAjax = $this->isAjax;
        $this->currentUrl = CommUtilLib::getCurrentUrl();
    }

    public function isRender() {
        if ($this->templateFile) {
            return true;
        }
        return false;
    }

    public function render($templateFile) {
        if ($templateFile) {
            $this->templateFile = $templateFile;
        }
    }

    public function assign($varName, $var) {
        $this->compile->assign($varName, $var);
    }

    public function cache($functionName, $rely = null, $time = 3600) {
        $this->compile->cache($functionName, $rely, $time);
    }

    public function display($templateFile = null) {
        $this->isDisplay = true;
        if ($templateFile) {
            $this->templateFile = $templateFile;
        }
        $this->compile();
        $this->compile->display();
    }

    public function getHtml($templateFile = null) {
        if ($templateFile) {
            $this->templateFile = $templateFile;
        }
        $this->compile();
        return $this->compile->getHtml();
    }

    public function responseError($msg = '', $code = 0) {
        if (is_array($msg) || is_object($msg)) {
            $msg = json_encode($msg);
        }
        throw new ViewExceptionLib($msg, $code);
    }

    public function redirect($url) {
        header('Location: ' . $url);
        Feng::sysExit();
    }

    public function closeWindow() {
        throw new OtherExceptionLib(OtherExceptionLib::CLOSE_WINDOW);
    }

    protected function postParam($name, $htmlspecialchars = false, $defaultValue = '') {
        $val = empty($_POST[$name]) ? $defaultValue : $_POST[$name];
        if ($htmlspecialchars) {
            $val = htmlspecialchars($val);
        }
        return $val;
    }

    protected function postInt($name, $defaultValue = 0) {
        return empty($_POST[name]) ? $defaultValue : (int) $_POST[$name];
    }

    protected function isPost() {
        return empty($_POST) ? false : true;
    }

    protected function getParam($name, $decode = false, $defaultValue = '') {
        $val = empty($_GET[$name]) ? $defaultValue : $_GET[$name];
        if ($decode) {
            $val = urldecode($val);
        }
        return $val;
    }

    protected function getInt($name, $defaultValue = 0) {
        return empty($_GET[name]) ? $defaultValue : (int) $_GET[$name];
    }

    protected function isGet() {
        return empty($_GET) ? false : true;
    }

    private function compile() {
        $this->compile->compile($this->templateFile);
    }

}
