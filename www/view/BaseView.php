<?php

/**
 * Description of BaseView
 *
 * @author hfdend
 */
class BaseView extends ViewCoreLib {

    protected $mustLogin = false;
    protected $currentUserModel;

    private static $userInfo = false;


    public function __construct() {
        parent::__construct();
        $this->getUserInfo();
        if ($this->mustLogin) {
            $this->mustLogin();
        }
    }
    
    /**
     * 必须登陆的操作钱执行
     */
    private function mustLogin() {
        if (!$this->currentUserModel) {
            $currentUrl = urlencode($this->currentUrl);
            $this->redirect('/passport/login?fromurl=' . $currentUrl);
        }
    }

    /**
     * 得到当前用户信息
     * @return type
     */
    private function getUserInfo() {
        if (self::$userInfo !== false) {
            $this->currentUserModel = self::$userInfo;
            return;
        }
        self::$userInfo = '';
        $result = empty($_COOKIE[UserUtil::USER_COOKIE_KEY]) ? '' : $_COOKIE[UserUtil::USER_COOKIE_KEY];
        $obj = json_encode($result);
        if (!empty($obj->Id)) {
            $this->currentUserModel = new UserDataModel();
            foreach ($this->currentUserModel as $key => $val) {
                if (isset($obj->$key)) {
                    $this->currentUserModel->$key = $obj->$key;
                }
            }
            self::$userInfo = $this->currentUserModel;
        }
    }

}