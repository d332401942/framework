<?php

class AjaxCoreLib extends ViewCoreLib {

    protected $isAjax = true;

    public function __construct() {
        parent::__construct();
    }

    public function responseError($msg, $code = 0) {
        $array = array(
            'error' => array(
                'message' => $msg,
                'code' => $code
            )
        );
        echo json_encode($array);
        $this->ajaxExit();
    }

    public function response($data) {
        $array = array(
            'result' => $data
        );
        echo json_encode($array);
        $this->ajaxExit();
    }

    private function ajaxExit() {
        throw new AjaxExceptionLib();
    }

}
