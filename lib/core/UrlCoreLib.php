<?php

class UrlCoreLib {

    public static $viewClass;

    public function parseUrl($regulation, $hostHeader = null) {
        $pathInfo = trim($_SERVER['REQUEST_URI'], '/');
        $pathInfo = preg_replace('/^(.*?)\.php/', '', $pathInfo);
        $pathInfo = trim($pathInfo, '/');
        $arr = explode('?', $pathInfo);
        $pathInfo = array_shift($arr);
        $matches = array();
        foreach ($regulation as $pattern => $arr) {
            if (preg_match($pattern, $pathInfo, $matches)) {
                $funcName = array_shift($arr);
                $needParameters = array();
                foreach ($arr as $v) {
                    if (!empty($matches[$v])) {
                        array_push($needParameters, $matches[$v]);
                    }
                }
                $class = new Request();
                $class->$funcName($needParameters, $hostHeader);
                return;
            }
        }
    }

    public static function getTplFileName($className) {
        $pathInfo = AutoLoad::strToPath($className);
        array_shift($pathInfo);
        $path = '';
        foreach ($pathInfo as $val) {
            $path .= '/' . $val;
        }
        $suffix = ltrim(config::TEMPLATE_FILE_TYPE, '.');
        $suffix = strtolower($suffix);
        $path = dirname($path) . '/' . $className . '.' . $suffix;
        return $path;
    }

}
