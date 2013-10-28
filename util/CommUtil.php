<?php

class CommUtil extends CommUtilLib {

    public static function setCookie($key, $value = '', $expire = '') {
        if ($value) {
            if (is_object($value) || is_array($value)) {
                $value = json_encode($value);
            } else if ($value === true) {
                $value = 1;
            } else if ($value === false) {
                $value = 0;
            }
            setcookie($key, $value, $expire, '/');
            $_COOKIE[$key] = $value;
        } else {
            setcookie($key, '', -1);
            if (isset($_COOKIE[$key])) {
                unset($_COOKIE[$key]);
            }
        }
    }

}