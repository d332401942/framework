<?php

class RegularDataBusiness extends BaseBusiness {
    
    /**
     * @return RegularDataBusiness
     */
    public static function getInstance() {
        return parent::getInstance();
    }
    
    /**
     * 得到所有的拥有类型
     * @return array
     */
    public function getSuperioritis() {
        $data = RegularDataData::getInstance();
        return $data->getSuperioritis();
    }
    
    /**
     * 通过key值查询拥有类型
     * @param type $key
     * @return string
     */
    public function getSuperiority($key) {
        $data = RegularDataData::getInstance();
        return $data->getSuperiority($key);
    }
    
    /**
     * 通过名称查询拥有类型的key
     * @param type $name
     * @return int
     */
    public function getSuperiorityKey($name) {
        $data = RegularDataData::getInstance();
        return $data->getSuperiorityKey($name);
    }
    
    /**
     * 得到所有的地区数据
     * @param Bool $isLevel 是否得到层级关系的数据
     * @return array
     */
    public function getAllArea($isLevel = true) {
        $data = RegularDataData::getInstance();
        return $data->getAllArea($isLevel);
    }
    
    /**
     * 得到所有的行业数据
     * @param Bool $isLevel 是否得到层级关系的数据
     * @return array
     */
    public function getAllIndustries($isLevel = true) {
        $data = RegularDataData::getInstance();
        return $data->getAllIndustries($isLevel);
    }
}
