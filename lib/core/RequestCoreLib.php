<?php

class RequestCoreLib extends Feng {

    public function httpRequest($parameters, $hostHeader = null) {
        $defFunc = Config::VIEW_FUNC;
        $viewStr = empty($parameters[0]) ? strtolower(Config::VIEW_DOLDER) : strtolower($parameters[0]);
        if ($hostHeader && $hostHeader != 'www') {
            $viewStr = $hostHeader . '/' . $viewStr;
        }
        $paramStr = empty($parameters[1]) ? '' : $parameters[1];

        $params = $this->getParams($paramStr);

        $className = null;
        UrlCoreLib::$viewClass = $this->getViewClass($viewStr, $className);

        $templateFile = self::getTempateFile($className);
        if (!UrlCoreLib::$viewClass->isRender()) {
            UrlCoreLib::$viewClass->render($templateFile);
        }
        LogVendorLib::start($className, $defFunc);
        UrlCoreLib::$viewClass->$defFunc($params);
        LogVendorLib::end($className, $defFunc);
        if (!UrlCoreLib::$viewClass->isDisplay) {
            UrlCoreLib::$viewClass->display();
        }
    }

    public function hostRequest($parameters, $hostHeader = null) {
        $viewStr = array_pop($parameters);
        $defFunc = Config::VIEW_FUNC;
        if ($hostHeader && $hostHeader != 'www') {
            $viewStr = $hostHeader . '/' . $viewStr;
        } else if (empty($viewStr)) {
            $viewStr = Config::VIEW_DOLDER;
        }

        $arr = pathinfo($viewStr);
        if (!empty($arr['extension'])) {
            throw new Exception('not found :: ' . $viewStr);
        }
        $viewStr = trim($viewStr, '/');
        $className = null;
        $params = $this->getParameters($viewStr, $className);
        $templateFile = self::getTempateFile($className);
        if (!UrlCoreLib::$viewClass->isRender()) {
            UrlCoreLib::$viewClass->render($templateFile);
        }
        LogVendorLib::start($className, $defFunc);
        UrlCoreLib::$viewClass->$defFunc($params);
        LogVendorLib::end($className, $defFunc);
        if (!UrlCoreLib::$viewClass->isDisplay) {
            UrlCoreLib::$viewClass->display();
        }
    }

    public function ajaxRequest($parameters) {
        $viewStr = empty($parameters[0]) ? strtolower(Config::VIEW_DOLDER) : strtolower($parameters[0]);
        $arr = explode('/', $viewStr);
        $defFunc = array_pop($arr);
        $viewStr = 'ajax/' . implode('/', $arr);
        $paramStr = empty($parameters[1]) ? '' : $parameters[1];
        $params = $this->getParams($paramStr);
        $className = null;
        UrlCoreLib::$viewClass = $this->getViewClass($viewStr, $className);
        LogVendorLib::start($className, $defFunc);
        try {
            UrlCoreLib::$viewClass->$defFunc($params);
        } catch (BusinessExceptionLib $e) {
            $message = $e->getMessage();
            $code = $e->getCode();
            $this->responseError($message, $code);
        } catch (AjaxExceptionLib $e) {
            $message = $e->getMessage();
            $code = $e->getCode();
            $this->responseError($message, $code);
        }
        LogVendorLib::end($className, $defFunc);
    }

    protected static function getTempateFile($className) {
        return rtrim(APP_DIR, '/') . '/' . config::TEMPLATE_DOLDER . UrlCoreLib::getTplFileName($className);
    }

    protected function getParameters($str, &$className) {
        $viewArr = explode('/', $str);
        $appDir = APP_DIR;
        if ($appDir == '') {
            $appDir = '.';
        }
        $path = rtrim($appDir, '/') . '/' . Config::VIEW_FOLDER;
        $className = ucfirst(Config::VIEW_FOLDER);
        $isFind = false;
        $parameters = array();
        for ($i = 0; $i < count($viewArr); $i++) {
            if (!$isFind) {
                $path .= '/' . $viewArr[$i];
                $className = ucfirst($viewArr[$i]) . $className;
                if (file_exists($path . '.php')) {
                    $isFind = true;
                }
            } else {
                $key = $viewArr[$i];
                $val = null;
                if (!empty($viewArr[$i + 1])) {
                    $val = $viewArr[$i + 1];
                }
                $parameters[$key] = $val;
                $i++;
            }
        }
        if ($i == count($viewArr)) {
            $path = rtrim($appDir, '/') . '/' . Config::VIEW_FOLDER;
            $folder = array_shift($viewArr);
            $path .= '/' . $folder . '/' . Config::VIEW_FILE . '.php';
            if (!file_exists($path)) {
                LogVendorLib::setWarning($path . '没有找到');
                throw new Exception('not found :: ' . $path);
            }
            $className = ucfirst(Config::VIEW_FILE) . ucfirst($folder) . ucfirst(Config::VIEW_FOLDER);
            for ($k = 0; $k < count($viewArr); $k++) {
                $key = $viewArr[$k];
                $val = null;
                if (!empty($viewArr[$k + 1])) {
                    $val = $viewArr[$k + 1];
                }
                $parameters[$key] = $val;
                $k++;
            }
        }
        UrlCoreLib::$viewClass = new $className($className);
        return $parameters;
    }

    protected function getViewClass($str, &$className) {
        $viewArr = explode('/', $str);
        if (count($viewArr) == 1) {
            array_push($viewArr, Config::VIEW_FILE);
        }
        $appDir = APP_DIR;
        if ($appDir == '') {
            $appDir = '.';
        }
        $path = rtrim($appDir, '/') . '/' . Config::VIEW_FOLDER;
        $preClassName = ucfirst(array_pop($viewArr));
        $lastClassName = null;
        foreach ($viewArr as $val) {
            $path .= '/' . $val;
            $lastClassName = ucfirst($val) . $lastClassName;
        }
        $className = $preClassName . $lastClassName . ucfirst(Config::VIEW_FOLDER);
        $path = $path . '/' . strtolower($preClassName);
        $includeFile = dirname($path) . '/' . $className . '.php';
        if (!file_exists($includeFile)) {
            LogVendorLib::setWarning($includeFile . '没有找到');
            throw new Exception('not found :: ' . $includeFile);
        }
        include_once $includeFile;
        array_push(LogVendorLib::$fireDebugInfo['加载文件'], $path);
        UrlCoreLib::$viewClass = new $className($className);
        return UrlCoreLib::$viewClass;
    }

    protected function getParams($str) {
        $params = array();
        $str = trim($str, '/');
        if (!$str) {
            return $params;
        }
        $arr = explode('/', $str);
        if (count($arr) % 2 != 0) {
            array_push($arr, null);
        }
        $i = 0;
        while ($i < count($arr)) {
            $params[$arr[$i]] = urldecode($arr[$i + 1]);
            $i += 2;
        }
        return $params;
    }

    private function responseError($msg, $code = 0) {
        if (!$msg && !$code) {
            return;
        }
        $array = array(
            'error' => array(
                'message' => $msg,
                'code' => $code
            )
        );
        echo json_encode($array);
    }

}
