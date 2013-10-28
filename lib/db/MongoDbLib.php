<?php

class MongoDbLib extends Feng {

    private $mongo;
    private $db;
    private $mongoFile;
    private $tableName;

    public function __construct() {
        LogVendorLib::start(__CLASS__, __FUNCTION__);
        $dataClassName = get_called_class();
        $this->tableName = preg_replace('/Data$/', '', $dataClassName);
        $this->getDb();
        LogVendorLib::end(__CLASS__, __FUNCTION__);
    }

    /**
     * 选择数据库
     * @param string $dbName
     *            数据库名
     */
    public function selectDb($connectString, $dbName) {
        LogVendorLib::start(__CLASS__, __FUNCTION__);
        try {
            $this->mongo = new Mongo($connectString);
            $this->db = $this->mongo->selectDB($dbName);
        } catch (Exception $e) {
            $this->throwDbException($e);
        }
        LogVendorLib::end(__CLASS__, __FUNCTION__);
    }

    /**
     * 返回一个连接的PDO类
     *
     * @return <PDO>
     */
    public function getDb() {
        LogVendorLib::start(__CLASS__, __FUNCTION__);
        if (!$this->db) {
            $this->selectDb(Config::MONGO_STRING, Config::MONGO_DBNAME);
        }
        LogVendorLib::end(__CLASS__, __FUNCTION__);
        return $this->db;
    }

    public function addFile($filePath, $info) {
        LogVendorLib::start(__CLASS__, __FUNCTION__);
        $mongoFile = $this->getMongoFile();
        $id = $mongoFile->put($filePath, $info);
        LogVendorLib::end(__CLASS__, __FUNCTION__);
        return $id;
    }

    public function getFileContent($id) {
        LogVendorLib::start(__CLASS__, __FUNCTION__);
        $mongoFile = $this->getMongoFile();
        $mongoId = new MongoId($id);
        $fileObj = $mongoFile->findOne(array('_id' => $mongoId));
        if (!$fileObj) {
            return false;
        }
        LogVendorLib::end(__CLASS__, __FUNCTION__);
        return $fileObj->getBytes();
    }

    public function getMongoFile() {
        if (!$this->mongoFile) {
            $this->mongoFile = $this->db->getGridFS($this->tableName);
        }
        return $this->mongoFile;
    }

    private function throwDbException($e) {
        throw new DbExceptionLib($e->getMessage(), $e->getCode(), $e);
    }

}
