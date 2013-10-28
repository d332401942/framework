<?php

class CacheDbLib extends Feng {

    private $redis = null;
    private $memcache = null;

    /**
     * @return CacheDbLib
     */
    public static function getInstance() {
        return parent::getInstance();
    }

    /**
     * 
     * @param type $host
     * @param type $port
     * @return RedisDbLib
     */
    public function getRedis($host = null, $port = null) {
        if (!$this->redis) {
            $this->redis = RedisDbLib::getInstance($host, $port);
        }
        return $this->redis;
    }

    /**
     * 
     * @return MemcacheDbLib
     */
    public function getMemcache() {
        if (!$this->memcache) {
            $this->memcache = MemcacheDbLib::getInstance();
        }
        return $this->memcache;
    }

}
