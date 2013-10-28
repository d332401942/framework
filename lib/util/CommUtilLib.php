<?php

class CommUtilLib extends Feng {

    public static function trimArr($arr) {
        $newArr = array();
        foreach ($arr as $key => $val) {
            if (is_string($val)) {
                $val = trim($val);
            }
            $newArr[$key] = $val;
        }
        return $newArr;
    }

    public static function setCookie($name, $value, $expire = 0, $path = '/') {
        setcookie($name, $value, $expire, $path);
        $_COOKIE[$name] = $value;
    }

    public static function rMkdir($path) {
        $path = $path . '/1';
        $arr = self::getDirTree($path);
        $arr = array_reverse($arr);
        foreach ($arr as $dir) {
            if (!file_exists($dir)) {
                mkdir($dir);
                //chmod($dir, 077);
            }
        }
    }

    private static function getDirTree($dir, $dirArr = array()) {
        $path = dirname($dir);
        array_push($dirArr, $path);
        if ($path == '.' || $path == '\\' || $path == '/') {
            return $dirArr;
        }

        return self::getDirTree($path, $dirArr);
    }

    public static function diffNow($time) {
        $now = time();
        $diff = $now - $time;
        if ($diff < 60) {
            return $diff . '秒';
        } else if ($diff < 3600) {
            $m = (int) ($diff / 60);
            return $m . '分钟';
        } else if ($diff < 24 * 3600) {
            $h = (int) ($diff / 3600);
            return $h . '小时';
        } else {
            $d = (int) ($diff / 24 / 3600);
            return $d . '天';
        }
    }

    public static function Obj2Array($obj) {
        $array = array();
        foreach ($obj as $key => $val) {
            if (is_array($val) || is_object($val)) {
                $array[$key] = self::Obj2Array($val);
            } else {
                $array[$key] = $val;
            }
        }
        return $array;
    }

    public static function truncate($string, $sublen = 80, $etc = '...', $break_words = false, $middle = false) {
        $start = 0;
        $code = "UTF-8";
        if ($code == 'UTF-8') {
            //如果有中文则减去中文的个数
            $cncount = self::cncount($string);

            if ($cncount > ($sublen / 2)) {
                $sublen = ceil($sublen / 2);
            } else {
                $sublen = $sublen - $cncount;
            }

            $pa = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/";
            $t_string = array();
            preg_match_all($pa, $string, $t_string);

            $str = join('', array_slice($t_string[0], $start, $sublen));
            $str = rtrim($str, '.');
            if (count($t_string[0]) - $start > $sublen) {
                return $str . $etc;
            } else {
                return $str;
            }
        } else {
            $start = $start * 2;
            $sublen = $sublen * 2;
            $strlen = strlen($string);
            $tmpstr = '';

            for ($i = 0; $i < $strlen; $i++) {
                if ($i >= $start && $i < ($start + $sublen)) {
                    if (ord(substr($string, $i, 1)) > 129) {
                        $tmpstr.= substr($string, $i, 2);
                    } else {
                        $tmpstr.= substr($string, $i, 1);
                    }
                }
                if (ord(substr($string, $i, 1)) > 129) {
                    $i++;
                }
            }
            if (strlen($tmpstr) < $strlen) {
                $tmpstr.= $etc;
            }
            return $tmpstr;
        }
    }

    public static function str2Arr($str, $code = 'utf-8') {
        $count = mb_strlen($str, $code);
        $arr = array();
        for ($i = 0; $i < $count; $i++) {
            $arr[] = mb_substr($str, $i, 1, $code);
        }
        return $arr;
    }

    public static function isChinese($str) {
        $regex = '/[\x{4e00}-\x{9fa5}]{3}/u';
        $matches = array();
        $is = preg_match($regex, $str);
        return $is;
    }

    private static function cncount($str) {
        $len = strlen($str);
        $cncount = 0;

        for ($i = 0; $i < $len; $i++) {
            $temp_str = substr($str, $i, 1);

            if (ord($temp_str) > 127) {
                $cncount++;
            }
        }
        return ceil($cncount / 3);
    }

    public static function getCurrentUrl() {
        $host = $_SERVER['HTTP_HOST'];
        $requestUri = $_SERVER['REQUEST_URI'];
        $url = 'http://' . $host . $requestUri;
        return $url;
    }

}
