<?php

/**
 * MakeXML
 * 
 * @author Lin Jiong(slime09@gmail.com)
 * @version v1.0
 * @license Copyright (c) 2009 Lin Jiong (www.cn09.com)
 * The LGPL (http://www.gnu.org/licenses/lgpl.html) licenses.
 */
/*
 * 从数组生成XML文件
 */
class XmlVendorLib extends Feng {

    private static $instance;

    public function __construct() {
        
    }

    /**
     * 获取XML字串
     *
     * @param $array 用于生成XML的数组,数组可以是二维或多维的，其中的第一个元素作为XML元素名        	
     * @param $xslName XSL文件名(如:"http://www.xxx.com/templates/normal/xslname.xsl")        	
     * @return $XMLString 输出XML字符串
     */
    public function getXML($array, $xslName = "", $isRn = false) {
        $lastStr = '';
        if ($isRn) {
            $lastStr = "\r\n";
        }
        $array = CommUtilLib::Obj2Array($array);
        $XMLString = '<?xml version="1.0" encoding="utf-8"?>' . $lastStr;
        if ($xslName != "")
            $XMLString .= '<?xml-stylesheet type="text/xsl" href="' . $xslName . '"?>' . $lastStr;
        $XMLString .= '<xml>' . $lastStr;
        $XMLString .= $this->make($array, $lastStr);
        $XMLString .= '</xml>';
        return $XMLString;
    }

    /*
     * 递归生成XML字串
     */

    private function make($array, $lastStr) {
        $XMLString = '';
        foreach ($array as $paramKey => $nodeParam) {
            if (is_numeric($paramKey)) {
                $paramKey = 'item';
            }
            if (!is_array($nodeParam)) {
                $XMLString .= '<' . $paramKey . '>' . htmlspecialchars($nodeParam) . '</' . $paramKey . '>' . $lastStr;
            } else {
                $XMLString .= '<' . $paramKey . '>' . $this->make($nodeParam, $lastStr) . '</' . $paramKey . '>' . $lastStr;
            }
        }

        return $XMLString;
    }

    /**
     * 将字串保存到文件
     *
     * @param $fileName 文件名        	
     * @param $XMLString 已经生成的XML字串        	
     */
    public function saveToFile($fileName, $XMLString) {
        if (!$handle = fopen($fileName, 'w')) {
            return FALSE;
        }
        if (!fwrite($handle, $XMLString)) {
            return FALSE;
        }
        return TRUE;
    }

    /**
     * 直接通过数组生成XML文件
     */
    public function write($fileName, $array, $xslName = '') {
        $XMLString = $this->getXML($array, $xslName);
        $result = $this->saveToFile($fileName, $XMLString);
        return $result;
    }

}
