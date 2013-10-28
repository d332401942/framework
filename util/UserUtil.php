<?php

class UserUtil extends Feng {
    
    const USER_COOKIE_KEY = 'UserInfo';
    
    /**
     * 设置用户信息到cookie
     * @param object $userModel
     */
    public static function setUserCookie($userModel) {
        unset($userModel->Password);
        CommUtil::setCookie(self::USER_COOKIE_KEY, $userModel);
    }
    
    /**
     * 删除用户信息
     */
    public static function delUserCookie() {
        CommUtil::setCookie(self::USER_COOKIE_KEY);
    }
    
    public static function checkUserIsLogin() {
        $result = empty($_COOKIE[self::USER_COOKIE_KEY]) ? '' : $_COOKIE[self::USER_COOKIE_KEY];
        $obj = json_decode($result);
        if (!empty($obj->Id)) {
            return false;
        }
        return true;
    }
    
}