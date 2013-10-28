<?php

class RegularDataData extends BaseData {

    /**
     * 有经验
     */
    const SUPERIORITY_EXPERIENCE = 1;

    /**
     * 有人脉
     */
    const SUPERIORITY_CONTACTS = 2;

    /**
     * 有资源
     */
    const SUPERIORITY_RESOURCES = 3;

    /**
     * 会管理
     */
    const SUPERIORITY_FUNDS = 4;

    /**
     * 有资金
     */
    const SUPERIORITY_MANAGEMENT = 5;

    /**
     * 懂技术
     */
    const SUPERIORITY_SKILL = 6;

    private $superiorities = array(
        self::SUPERIORITY_EXPERIENCE => '有经验',
        self::SUPERIORITY_CONTACTS => '有人脉',
        self::SUPERIORITY_RESOURCES => '有资源',
        self::SUPERIORITY_FUNDS => '会管理',
        self::SUPERIORITY_MANAGEMENT => '有资金',
        self::SUPERIORITY_SKILL => '懂技术',
    );
    private static $regularData = array();

    public function __construct() {
        
    }

    /**
     * @return RegularDataData
     */
    public static function getInstance() {
        return parent::getInstance();
    }

    /**
     * 得到所有的拥有类型
     * @return array
     */
    public function getSuperioritis() {
        return $this->superiorities;
    }

    /**
     * 通过key值查询拥有类型
     * @param type $key
     * @return type
     */
    public function getSuperiority($key) {
        return empty($this->superiorities[$key]) ? false : $this->superiorities[$key];
    }
    
    /**
     * 通过名称查询拥有类型的key
     * @param type $name
     * @return type
     */
    public function getSuperiorityKey($name) {
        $tmpArr = array_reverse($this->superiorities);
        return empty($tmpArr[$name]) ? false : $tmpArr[$name];
    }

    /**
     * 得到所有的地区数据
     * @param Bool $isLevel 是否得到层级关系的数据
     * @return array
     */
    public function getAllArea($isLevel = true) {
        $result = $this->getRegularData('area');
        if ($isLevel) {
            $result = $this->getLevelData($result, 'area');
        }
        return $result;
    }

    /**
     * 得到所有的行业数据
     * @param Bool $isLevel 是否得到层级关系的数据
     * @return type
     */
    public function getAllIndustries($isLevel = true) {
        $result = $this->getRegularData('industry');
        if ($isLevel) {
            $result = $this->getLevelData($result);
        }
        return $result;
    }

    /**
     * 把数据变的有层级关系
     * @param type $data
     * @param type $type
     * @return type
     */
    private function getLevelData($data) {
        $result = array();
        foreach ($data as $id => $model) {
            $pid = $model->Pid;
            if ($model->Level != 1 && !empty($data[$pid])) {
                $data[$pid]->Children[$id] = $model;
            }
        }
        foreach ($data as $id => $model) {
            if ($model->Level == 1) {
                $result[$id] = $model;
            }
        }
        return $result;
    }

    private function getRegularData($type) {
        $tableName = null;
        $modelName = '';
        $type = strtolower($type);
        $cacheKey = 'regular_data_' . $type;
        if (!empty(self::$regularData[$type])) {
            return self::$regularData[$type];
        }
        $cache = CacheDbLib::getInstance()->getMemcache();
        $result = $cache->get($cacheKey);
        if ($result) {
            return $result;
        }
        switch ($type) {
            case 'area':
                $tableName = 'Area';
                $modelName = 'AreaDataModel';
                break;
            case 'industry':
                $tableName = 'Industry';
                $modelName = 'IndustryDataModel';
                break;
        }
        if (!$tableName) {
            return;
        }
        $sql = 'select * from `' . $tableName . '`';
        $statement = $this->run($sql);
        $result = array();
        while ($model = $statement->fetchObject($modelName)) {
            $result[$model->Id] = $model;
        }
        self::$regularData[$type] = $result;
        $cache->set($cacheKey, $result, MEMCACHE_COMPRESSED);
        return $result;
    }

}
