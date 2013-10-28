<?php

class RemoteCoreLib extends Feng {

    public function request($url, $data, $cookie = array()) {
        $urlInfo = parse_url($url);
        $host = $urlInfo['host'];
        $path = $urlInfo['path'];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_COOKIE, self::parseCookie($cookie));
        curl_setopt($ch, CURLOPT_POSTFIELDS, self::parsePost($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    private static function parseCookie($data) {
        return self::parseArrToStr($data, ';');
    }

    private static function parsePost($data) {
        return self::parseArrToStr($data, '&');
    }

    private static function parseArrToStr($data, $glue = '&', $parentKey = '') {
        $str = '';
        foreach ($data as $key => $val) {
            if ($parentKey) {
                $marks = '';
                $key = $parentKey . '[' . $key . ']';
            }
            if (is_array($val) || is_object($val)) {
                $str .= self::parseArrToStr($val, $glue, $key);
            } else {
                $str .= $glue . $key . '=' . $val;
            }
        }
        if (!$parentKey) {
            $str = ltrim($str, $glue);
        }
        return $str;
    }

}
