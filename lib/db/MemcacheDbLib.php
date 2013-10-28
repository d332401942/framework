<?php

class MemcacheDbLib extends Memcache {

    private static $class;

    /**
     * @return MemcacheDbLib
     */
    public static function getInstance() {
        if (empty(self::$class)) {
            self::$class = new MemcacheDbLib();
        }
        return self::$class;
    }

    public function __construct() {
        $memServerArr = explode(',', Config::MEMCACHE_SERVER);
        foreach ($memServerArr as $str) {
            $arr = explode(':', $str);
            $this->addServer($arr[0], $arr[1]);
            //$this->setCompressThreshold(20000, 0.2);
        }
    }

}
