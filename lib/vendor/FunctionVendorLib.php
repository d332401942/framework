<?php

class FunctionVendorLib extends Feng {

    public function cut($string, $parame) {
        return '：-->' . $string . '-----我的位置在' . __FILE__ . '--' . __CLASS__ . '--' . __FUNCTION__ . '--参数' . $parame;
    }

    public function truncate($string, $sublen = 80, $etc = '...', $break_words = false, $middle = false) {
        $str = CommUtilLib::truncate($string, $sublen, $etc, $break_words, $middle);
        return $str;
    }

    public function date_format($stamp, $format) {
        return date($format, $stamp);
    }

    public function parsePicpath($path) {
        if (!preg_match('/^http:\/\//', $path) && !preg_match('/^\//', $path)) {
            $path = '/' . $path;
        }
        return $path;
    }

    public function searchPicpath($path) {
        $host = Config::STATIC_HOST;
        if (!preg_match('/^http:\/\//', $path) && !preg_match('/^\//', $path)) {
            $path = $host . '/' . $path;
        }
        return $path;
    }

    public function numerRound($numer, $n) {
        return round($numer, $n);
    }

    public function screenUrl($url) {
        if (strlen($url) < 4) {
            return $url;
        }
        $num = strrchr($url, 'http');
        if (!$num) {
            return $url;
        }
        $arr = explode('http', $url);
        $url = 'http' . array_pop($arr);
        $url = urldecode($url);

        return $url;
    }

    public function diffNow($time) {
        return CommUtilLib::diffNow($time);
    }

}
