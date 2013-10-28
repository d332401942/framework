<?php

class ModelCoreLib extends Feng {

    const IS_YES = 1;
    const IS_NO = 0;

    private $priMaryKey;
    private $tableName;
    private $workFields = array();
    private $ignoreFields = array();

    public function __construct() {
        $modelName = get_called_class();
        $tableName = str_replace('DataModel', '', $modelName);
        $tableName = config::DB_PRE_TABLENAME . $tableName . config::DB_LAST_TABLENAME;
        $this->setTableName($tableName);
        $this->setIgoneFields('priMaryKey');
        $this->setIgoneFields('tableName');
        $this->setIgoneFields('workFields');
        $this->setIgoneFields('ignoreFields');
    }

    public function setPrimaryKey($field) {
        $this->priMaryKey = $field;
    }

    public function getPrimaryKey() {
        return $this->priMaryKey;
    }

    public function setTableName($tableName) {
        $this->tableName = $tableName;
    }

    public function getTableName() {
        return $this->tableName;
    }

    public function setWorkFields($fields) {
        if (!is_array($fields)) {
            $fields = array($fields);
        }
        $this->workFields = array_merge($this->workFields, $fields);
        $this->workFields = array_unique($this->workFields);
    }

    public function clearWorkFields() {
        $this->workFields = array();
    }

    public function getWorkFields() {
        $fields = array();
        $workFields = $this->workFields;
        $igoneFields = $this->getIgoneFields();
        $primaryKey = $this->getPrimaryKey();
        foreach (get_class_vars(get_called_class()) as $key => $val) {
            if ($key == $primaryKey || in_array($key, $igoneFields)) {
                continue;
            }
            if ($workFields && !in_array($key, $workFields)) {
                continue;
            }
            array_push($fields, $key);
        }
        return $fields;
    }

    public function toArray() {
        $array = array();
        $workFields = $this->workFields;
        $igoneFields = $this->getIgoneFields();
        $primaryKey = $this->getPrimaryKey();
        foreach (get_class_vars(get_called_class()) as $key => $val) {
            if (in_array($key, $igoneFields)) {
                continue;
            }
            $array[$key] = $this->$key;
        }
        return $array;
    }

    public function loadArray($arr) {
        if (empty($arr) || !is_array($arr)) {
            return;
        }
        $igoneFields = $this->getIgoneFields();
        foreach ($arr as $key => $val) {
            if (in_array($key, $igoneFields)) {
                continue;
            }
            $this->$key = $val;
        }
    }

    public function setIgoneFields($varNames) {
        if (!is_array($varNames)) {
            $varNames = array($varNames);
        }
        $this->ignoreFields = array_merge($this->ignoreFields, $varNames);
        $this->ignoreFields = array_unique($this->ignoreFields);
    }

    public function getIgoneFields() {
        return $this->ignoreFields;
    }

}
