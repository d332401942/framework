<?php

class CompileVendorLib extends Feng {

    private $tplFile;
    private $complieFile;
    private $complieCacheFile;
    private $isHasCache = false;
    private $content;
    private $storage = array();
    private $funcIsRuned = false;
    private $cacheInfo = array();
    private static $cacheNum = 0;

    public function compile($tplFile) {
        $this->tplFile = $tplFile;
        $this->storage ['myTempCahceClass'] = new CacheVendorLib ();
        $this->complieCacheFile = str_replace(Config::TEMPLATE_DOLDER, config::COMPILE_DOLDER, $this->tplFile) . '_cache_{num}.php';
        if (!file_exists($this->tplFile)) {
            throw new Exception('模板文件[' . $this->tplFile . ']不存在');
        }
        $tplMTime = file_exists($this->tplFile) ? filemtime($this->tplFile) : 0;
        $relyStr = '';
        foreach ($this->cacheInfo as $arr) {
            if ($arr) {
                $relyStr .= '_' . $arr [0];
            }
        }
        $this->complieFile = str_replace(Config::TEMPLATE_DOLDER, config::COMPILE_DOLDER, $this->tplFile) . $relyStr . '.php';
        if (file_exists($this->complieFile)) {
            $compileMTime = filemtime($this->complieFile);
            if ($tplMTime < $compileMTime && time() - $compileMTime < config::COMPILE_FILE_LIFE_TIME) {
                return;
            }
        }
        $this->mkComplieDir();
        touch($this->complieFile);
        // chmod($this->complieFile, 0777);
        $content = file_get_contents($this->tplFile);
        $content = $this->parseContent($content);
        $this->content = $content;
        file_put_contents($this->complieFile, $this->content);
    }

    public function assign($varName, $var) {
        $this->storage [$varName] = $var;
    }

    public function cache($functionName, $rely = null, $time = 3600) {
        $this->cacheInfo [$functionName] = array(
            $rely,
            $time
        );
    }

    public function display() {
        if (file_exists('./utility/function.php')) {
            if (!class_exists('FunctionUtility')) {
                include './utility/function.php';
            }
            $this->func = new FunctionUtility ();
        } else {
            $this->func = new FunctionVendorLib ();
        }
        ob_start();
        include $this->complieFile;
        $content = ob_get_contents();
        ob_end_clean();
        if ($this->isHasCache) {
            $this->cacheContent($content);
        }
        echo $content;
    }

    public function getHtml() {
        if (defined(Confg::COMPILE_FUNCTIONS_CLASS)) {
            $className = Confg::COMPILE_FUNCTIONS_CLASS;
            $this->func = new $className();
        } else {
            $this->func = new FunctionVendorLib();
        }
        ob_start();
        include $this->complieFile;
        $content = ob_get_contents();
        ob_clean();
        return $content;
    }

    private function mkComplieDir() {
        $arr = $this->getDirTree($this->complieFile);
        $arr = array_reverse($arr);
        foreach ($arr as $path) {
            if (!file_exists($path)) {
                mkdir($path);
            }
        }
    }

    private function getDirTree($dir, $dirArr = array()) {
        $path = dirname($dir);
        array_push($dirArr, $path);
        if ($path == rtrim(APP_DIR, '/') . '/' . Config::COMPILE_DOLDER || $path == '.') {
            return $dirArr;
        }

        return $this->getDirTree($path, $dirArr);
    }

    private function parseContent($content) {
        $leftLimit = preg_quote(config::COMPILE_RIGHT_LEFT);
        $rightLimit = preg_quote(config::COMPILE_RIGHT_LIMIT);
        $patternInclude = '/' . $leftLimit . 'include\s(.*)' . $rightLimit . '/U';
        $content = preg_replace_callback($patternInclude, array(
            __CLASS__,
            'replaceInclude'
                ), $content);
        $content = $this->replaceDefine($content);
        $pattern = '/' . $leftLimit . '(.*)' . $rightLimit . '/sU';
        $patternTrim = '/' . $rightLimit . '([.\s]*)' . $leftLimit . '/U';
        $patternCache = '/' . $leftLimit . 'cache\s?(.*?)' . $rightLimit . '(.*?)' . $leftLimit . '\/cache' . $rightLimit . '/s';
        $content = preg_replace_callback($patternTrim, array(
            __CLASS__,
            'trimContent'
                ), $content);
        $content = preg_replace_callback($pattern, array(
            __CLASS__,
            'preReplace'
                ), $content);
        $content = preg_replace_callback($patternCache, array(
            __CLASS__,
            'cacheReplace'
                ), $content);
        return $content;
    }

    private function replaceDefine($content) {
        $resourcePath = APP_ROOT . '/' . APP_DIR . '/' . trim(Config::RESOURCE_DOLDER, '/');
        $appPath = APP_URL == '/' ? '' : APP_URL;
        $templatePath = rtrim(APP_DIR, '/') . '/' . Config::TEMPLATE_DOLDER;
        $content = str_replace('__RESOURCE__', $resourcePath, $content);
        $content = str_replace('__APP__', $appPath, $content);
        $content = str_replace('__PUBLIC__', ltrim(APP_ROOT, '/') . '/public', $content);
        $content = str_replace('__TEMPLATE__', $templatePath, $content);
        $content = str_replace('__LIMIT__', Config::URL_LIMIT_GET, $content);
        $content = str_replace('__AJAX__', $appPath . '/ajax', $content);
        return $content;
    }

    private function replaceInclude($matches) {
        $file = trim($matches [1], ' \'"');
        if (empty($file)) {
            return '';
        }
        $file = $this->replaceDefine($file);
        $content = file_get_contents($file);
        $content = $this->replaceDefine($content);
        return $content;
    }

    private function trimContent($matches) {
        return config::COMPILE_RIGHT_LIMIT . preg_replace('/\s/', ' ', $matches [1]) . config::COMPILE_RIGHT_LEFT;
    }

    private function preReplace($matches) {
        $patterns = array(
            '/\$(\w+)/',
            '/^\$(.*)/',
            '/^(foreach|for|if|while|do|try|switch|catch)(.*)/',
            '/^else(.*)/',
            '/^\/case/',
            '/^\/.*/'
        );
        $replace = array(
            '\$this->storage[\'${1}\']',
            'echo \$${1}',
            '${1}${2} {',
            '} else${1} {',
            'break',
            '}'
        );
        if (!preg_match('/^\/cache|^cache/', $matches [1])) {
            $content = preg_replace($patterns, $replace, trim($matches [1]));
            $content = $this->preFunction($content);
            // control模版
            $content = $this->parseControl($content);
            $content = '<?php ' . $content . ' ?>';
        } else {
            $content = config::COMPILE_RIGHT_LEFT . $matches [1] . Config::COMPILE_RIGHT_LIMIT;
        }
        return $content;
    }

    private function cacheReplace($content) {
        $this->isHasCache = true;
        $functionName = trim($content [1]);
        $caheInfo = $this->cacheInfo [$functionName];
        $rely = $caheInfo [0];
        $cacheTime = $caheInfo [1];
        $complieCacheFile = str_replace('{num}', self::$cacheNum . '_' . $rely, $this->complieCacheFile);
        $html = Config::COMPILE_RIGHT_LEFT . $complieCacheFile . Config::COMPILE_RIGHT_LIMIT . $content [2] . Config::COMPILE_RIGHT_LEFT . '/' . Config::COMPILE_RIGHT_LIMIT;

        $needPutContent = true;
        if (file_exists($complieCacheFile)) {
            $cacheFilemtime = filemtime($complieCacheFile);
            $tempFilemtime = filemtime($this->tplFile);
            if ($cacheFilemtime > $tempFilemtime && (time() - $cacheFilemtime) < $cacheTime) {
                $needPutContent = false;
            }
        }
        if ($needPutContent) {
            $this->runCacheFunction($functionName);
            file_put_contents($complieCacheFile, $html);
        }
        self::$cacheNum ++;
        return '<?php include \'' . $complieCacheFile . '\' ?>';
    }

    private function runCacheFunction($functionName) {
        UrlCoreLib::$viewClass->$functionName();
    }

    private function cacheContent(&$content) {
        $left = preg_quote(Config::COMPILE_RIGHT_LEFT);
        $right = preg_quote(Config::COMPILE_RIGHT_LIMIT);
        $pattern = '/' . $left . '(.*?)' . $right . '(.*?)' . $left . '\/' . $right . '/s';
        $matches = array();
        preg_match_all($pattern, $content, $matches);
        for ($i = 0; $i < count($matches [0]); $i ++) {
            $file = $matches [1] [$i];
            $html = $matches [2] [$i];
            // 去除执行PHP代码
            $html = str_replace('<?', '<!--?', $html);
            $html = str_replace('?>', '?-->', $html);
            file_put_contents($file, $html);
        }
        $content = preg_replace('/' . $left . '.*?' . $right . '/', '', $content);
    }

    private function preFunction($content) {
        if (strpos($content, '|')) {
            $array = explode('|', $content);
            $array = CommUtilLib::trimArr($array);
            $isEcho = false;
            $value = $array [0];
            if (stripos($value, 'echo') !== false) {
                $isEcho = true;
                $value = str_replace('echo', '', $value);
                $value = trim($value);
            }
            $other = $array [1];
            $tempArr = array();
            preg_match_all('/(\'.*?\')|(".*?")/', $other, $tempArr);
            $tt = array();
            if ($tempArr) {
                $arr = array_shift($tempArr);
                if ($arr && is_array($arr)) {
                    foreach ($arr as $key => $val) {
                        $tt [$key . '@HF!!@@##$$E'] = $val;
                        $other = str_replace($val, $key . '@HF!!@@##$$E', $other);
                    }
                }
            }
            $arr = explode(':', $other);
            if ($tempArr) {
                foreach ($arr as $k => $v) {
                    foreach ($tt as $kk => $vv) {
                        $arr [$k] = str_replace($kk, $vv, $arr [$k]);
                    }
                }
            }
            $function = array_shift($arr);
            $content = '';
            if ($isEcho) {
                $content = 'echo ';
            }
            $parameters = $value . ',' . implode(',', $arr);
            $parameters = trim($parameters, ',');
            $content .= '$this->func->' . $function . '(' . $parameters . ')';
        }
        return $content;
    }

    private function parseControl($content) {
        if (strtolower(substr($content, 0, 2)) != 'uc') {
            return $content;
        }
        $str = trim(substr($content, 2));
        if (!$str) {
            return $content;
        }
        $arr = explode(' ', $str);
        $content = '$this->getControlHtml(';
        $content .= 'array(';
        foreach ($arr as $val) {
            if (!$val) {
                continue;
            }
            $array = explode('=', $val);
            $kk = !empty($array [0]) ? trim($array [0]) : null;
            $vv = !empty($array [1]) ? trim($array [1]) : null;
            if ($vv && substr($vv, 0, 2) == '"$') {
                $vv = trim($vv, '"');
            }
            if ($kk) {
                if (strtolower($kk) == 'class') {
                    $kk = 'class';
                }
                $content .= '\'' . $kk . '\' => ' . $vv . ',';
            }
        }
        $content .= ')';
        $content .= ');';
        return $content;
    }

    private function getControlHtml($parameters) {
        $className = $parameters ['class'];
        unset($parameters ['class']);

        $controlClass = new $className ();
        foreach ($parameters as $key => $val) {
            $controlClass->assign($key, $val);
        }
        $defFunc = config::VIEW_FUNC;
        $controlClass->$defFunc($parameters);
        $templateFile = rtrim(APP_DIR, '/') . '/' . config::TEMPLATE_DOLDER . UrlCoreLib::getTplFileName($className);

        if (file_exists($templateFile)) {
            if (!$controlClass->isRender()) {
                $controlClass->render($templateFile);
            }
            if (!$controlClass->isDisplay) {
                $controlClass->display();
            }
        } else if (Config::FIRE_DEBUG) {
            FB::warn('模版文件：' . $templateFile . '没有找到');
        }
    }

}
