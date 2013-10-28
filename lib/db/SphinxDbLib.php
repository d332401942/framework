<?php

include __DIR__ . '/sphinxapi.php';

class SphinxDbLib extends SphinxClient {

    public function __construct($server = null, $port = null) {
        parent::__construct();
        $server = $server ? $server : Config::SPHINX_SERVER;
        $port = $port ? $port : Config::SPHINX_PORT;
        $this->connect($server, $port);
        $this->setMatchMode(SPH_MATCH_EXTENDED2);
    }

    public function getResultIds($result, $pageCore = null) {
        $resultIds = array();
        if (!empty($result['matches'])) {
            $resultIds = array_keys($result['matches']);
            if ($pageCore) {
                $pageCore->count = $result['total_found'];
                $pageCore->pageCount = ceil($pageCore->count / $pageCore->pageSize);
            }
        }
        return $resultIds;
    }

    public function getLightWords($result) {
        $lightWords = array();
        if (!empty($result['words'])) {
            $lightWords = array_keys($result['words']);
            usort($lightWords, array(__CLASS__, 'sortByLength'));
        }
        return $lightWords;
    }

    public function buildExcerpts($docs, $index, $words, $opts = array()) {
        $array = array(
            'docs' => $docs,
            'index' => $index,
            'words' => $words,
            'opts' => $opts,
        );
        LogVendorLib::dbStart(__CLASS__, __FUNCTION__, $array, LogVendorLib::KEY_DB_SPHINX);
        $result = parent::buildExcerpts($docs, $index, $words, $opts);
        LogVendorLib::dbEnd(__CLASS__, __FUNCTION__);
        return $result;
    }

    public function setSortMode($mode = SPH_SORT_EXTENDED, $sortby = null) {
        $array = array(
            'mode' => $mode,
            'sortby' => $sortby,
        );
        LogVendorLib::dbStart(__CLASS__, __FUNCTION__, $array, LogVendorLib::KEY_DB_SPHINX);
        $result = parent::setSortMode($mode, $sortby);
        LogVendorLib::dbEnd(__CLASS__, __FUNCTION__);
        return $result;
    }

    public function setFieldWeights($weights) {
        LogVendorLib::dbStart(__CLASS__, __FUNCTION__, $weights, LogVendorLib::KEY_DB_SPHINX);
        $result = parent::setFieldWeights($weights);
        LogVendorLib::dbEnd(__CLASS__, __FUNCTION__);
        return $result;
    }

    public function query($keyword, $index = '*', $comment = '') {
        $array = array(
            'keyword' => $keyword,
            'index' => $index,
            'comment' => $comment,
        );
        LogVendorLib::dbStart(__CLASS__, __FUNCTION__, $array, LogVendorLib::KEY_DB_SPHINX);
        $result = parent::query($keyword, $index, $comment);
        LogVendorLib::dbEnd(__CLASS__, __FUNCTION__);
        return $result;
    }

    public function setFilter($attribute, $values, $exclude = false) {
        $array = array(
            'attribute' => $attribute,
            'values' => $values,
            'exclude' => $exclude,
        );
        LogVendorLib::dbStart(__CLASS__, __FUNCTION__, $array, LogVendorLib::KEY_DB_SPHINX);
        $result = parent::setFilter($attribute, $values, $exclude);
        LogVendorLib::dbEnd(__CLASS__, __FUNCTION__);
        return $result;
    }

    public function setFilterRange($attribute, $min, $max, $exclude = false) {
        $array = array(
            'attribute' => $attribute,
            'min' => $min,
            'max' => $max,
            'exclude' => $exclude,
        );
        LogVendorLib::dbStart(__CLASS__, __FUNCTION__, $array, LogVendorLib::KEY_DB_SPHINX);
        $result = parent::setFilterRange($attribute, $min, $max, $exclude);
        LogVendorLib::dbEnd(__CLASS__, __FUNCTION__);
        return $result;
    }

    public function setFilterFloatRange($attribute, $min, $max, $exclude = false) {
        $array = array(
            'attribute' => $attribute,
            'min' => $min,
            'max' => $max,
            'exclude' => $exclude,
        );
        LogVendorLib::dbStart(__CLASS__, __FUNCTION__, $array, LogVendorLib::KEY_DB_SPHINX);
        $result = parent::setFilterFloatRange($attribute, $min, $max, $exclude);
        LogVendorLib::dbEnd(__CLASS__, __FUNCTION__);
        return $result;
    }

    public function setGroupBy($attribute, $func = SPH_GROUPBY_ATTR, $groupsort = '@group desc') {
        $array = array(
            'attribute' => $attribute,
            'func' => $func,
            'groupsort' => $groupsort,
        );
        LogVendorLib::dbStart(__CLASS__, __FUNCTION__, $array, LogVendorLib::KEY_DB_SPHINX);
        $result = parent::setGroupBy($attribute, $func, $groupsort);
        LogVendorLib::dbEnd(__CLASS__, __FUNCTION__);
        return $result;
    }

    public function clear() {
        $this->resetFilters();
        $this->resetGroupBy();
    }

    private static function sortByLength($a, $b) {
        $al = mb_strlen($a, 'utf-8');
        $bl = mb_strlen($b, 'utf-8');
        return ($a < $b) ? -1 : 1;
    }

    private function connect($server, $port) {
        $arr = explode(',', $port);
        $rand = mt_rand(0, count($arr) - 1);
        $port = (int) $arr[$rand];
        $this->setServer($server, $port);
    }

}
