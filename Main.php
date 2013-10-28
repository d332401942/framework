<?php

@umask(0002);
ini_set('memory_limit', '2048M');
ini_set('display_errors', 1);
error_reporting(E_ALL);
set_error_handler('customError');
header("Content-Type:text/html;charset=utf-8");
date_default_timezone_set("PRC");
$filePath = dirname(__FILE__);
session_start();
require $filePath . '/Feng.php';
require $filePath . '/Conf.php';
require $filePath . '/lib/FirePHPCore/fb.php';
//项目目录
if (!defined('APP_DIR')) {
    define('APP_DIR', '.');
}
if (!defined('APP_ROOT')) {
    define('APP_ROOT', '.');
}
if (!defined('APP_URL')) {
    define('APP_URL', '.');
}
require rtrim(APP_DIR, '/') . '/Config.php';
if (Config::FIRE_DEBUG) {
    require __DIR__ . '/kint/Kint.class.php';
    Kint::enabled(false);
}

function __autoload($className) {
    if (class_exists($className) || !class_exists('AutoLoad')) {
        return;
    }
    AutoLoad::includeFile($className);
}

function customError($errno, $errstr, $errfile, $errline) {
    if (!(error_reporting() & $errno)) {
        // This error code is not included in error_reporting
        return;
    }
    switch ($errno) {
        case E_ERROR:
            $errortype = '[MERROR:]';
            break;

        case E_WARNING:
            $errortype = '[WARNING:]';
            break;

        case E_NOTICE:
            $errortype = '[NOTICE:]';
            break;
        default:
            $errortype = '[Unknown error type:]';
            break;
    }
    $errorMsg = $errortype . $errstr . ' in ' . $errfile . ' on line ' . $errline;
    echo $errorMsg . '<br>';
    LogVendorLib::setSysError($errorMsg);
}

class AutoLoad extends Feng {

    public static function strToPath($str) {
        $upper = range('A', 'Z');
        $arr = str_split($str);
        $str = null;
        $autoArr = array();
        foreach ($arr as $val) {
            if (in_array($val, $upper)) {
                $autoArr [] = strtolower($str);
                $str = $val;
            } else {
                $str .= $val;
            }
        }
        $autoArr [] = strtolower($str);
        array_shift($autoArr);
        $autoArr = array_reverse($autoArr);
        return $autoArr;
    }

    public static function includeFile($className) {
        $autoArr = self::strToPath($className);
        self::includeByArr($autoArr, $className);
    }

    private static function includeByArr($autoArr, $className) {

        $preName = rtrim(APP_ROOT, '/');
        $current = current($autoArr);
        if ($current == 'lib') {
            $preName = dirname(__FILE__);
        } else if ($current == strtolower(Config::VIEW_FOLDER)) {
            $preName = rtrim(APP_DIR, '/');
        }



        $filePath = '';
        foreach ($autoArr as $val) {
            $filePath .= '/' . $val;
        }

        $includeFilePath = dirname($preName . $filePath) . '/' . $className;

        $includeFilePath .= '.php';
        if (file_exists($includeFilePath)) {
            include $includeFilePath;
            if (!isset(LogVendorLib::$fireDebugInfo[LogVendorLib::KEY_AUTO_LOAD_FILE])) {
                LogVendorLib::$fireDebugInfo[LogVendorLib::KEY_AUTO_LOAD_FILE] = array();
            }
            if (!in_array($includeFilePath, LogVendorLib::$fireDebugInfo[LogVendorLib::KEY_AUTO_LOAD_FILE])) {
                array_push(LogVendorLib::$fireDebugInfo[LogVendorLib::KEY_AUTO_LOAD_FILE], $includeFilePath);
            }
        } else {
            $filePath = './' . $filePath;
            $lastName = array_pop($autoArr);
            $i = count($autoArr);
            if ($i <= 0) {
                return;
            }
            $autoArr[$i - 1] = $lastName . $autoArr[$i - 1];
            self::includeByArr($autoArr, $className);
        }
    }

}

class Main extends Feng {

    public static $isAjax = false;

    public static function run($regulation = array(), $hostHeader = null) {
        require rtrim(APP_DIR, '/') . '/Request.php';
        if (empty($regulation)) {
            $regulation = array(
                '/.*/' => array('httpRequest', 0),
            );
        }
        $urlClass = new UrlCoreLib();
        try {
            $urlClass->parseUrl($regulation, $hostHeader);
            if (Config::FIRE_DEBUG) {
                LogVendorLib::deBug();
            }
            if (Config::LOG_RUN_IS_OPEN) {
                LogVendorLib::write();
            }
        } catch (OtherExceptionLib $e) {
            if (Config::FIRE_DEBUG) {
                LogVendorLib::deBug();
            }
            if (Config::LOG_RUN_IS_OPEN) {
                LogVendorLib::write();
            }
        } catch (AjaxExceptionLib $e) {
            if (Config::FIRE_DEBUG) {
                LogVendorLib::deBug();
            }
            if (Config::LOG_RUN_IS_OPEN) {
                LogVendorLib::write();
            }
        } catch (Exception $e) {
            LogVendorLib::setException($e);
            if (Config::FIRE_DEBUG) {
                LogVendorLib::deBug();
            }
            if (Config::LOG_RUN_IS_OPEN) {
                LogVendorLib::write();
            }
            //header("HTTP/1.0 404 Not Found");
            throw $e;
        }
    }

    public function notFound($fileName) {
        include $fileName;
    }

}

/**
 * 
 * @param unknown $className
 * @return $className
 */
function M($className) {
    static $fengMClassNameToModel = array();
    if (isset($fengMClassNameToModel[$className])) {
        $model = $fengMClassNameToModel[$className];
    } else {
        $model = new $className();
        $fengMClassNameToModel[$className] = $model;
    }
    return $model;
}

function P() {
    $args = func_get_args(); // 获取多个参数
    if (count($args) < 1) {
        return;
    }

    echo '<div style = "width:100%;text-align:left"><pre>';
    // 多个参数循环输出
    foreach ($args as $arg) {
        if (is_array($arg)) {
            print_r($arg);
            echo '<br>';
        } else if (is_string($arg)) {
            echo $arg . '<br>';
        } else if (is_object($arg)) {
            print_r($arg);
            echo '<br>';
        } else {
            var_dump($arg);
            echo '<br>';
        }
    }
    echo '</pre></div>';
}
