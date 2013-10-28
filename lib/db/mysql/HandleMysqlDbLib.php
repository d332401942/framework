<?php

class HandleMysqlDbLib extends Feng {

    /**
     * PDO对象
     *
     * @var PDO<PDO>
     */
    private $pdo;

    /**
     * 默认表名称
     *
     * @var string
     */
    private $tableName;

    /**
     * 默认DataMode
     *
     * @var object
     */
    private $dataModel;

    /**
     * 默认DataMode名称
     *
     * @var string
     */
    private $modelName;

    /**
     * PDO预处理对象
     *
     * @var <PDOStatement>
     */
    private $sth;
    private $pageCore;
    private $where = array();
    private $whereTmp = array();
    private $line = array();
    private $order = array();
    private $limit = array();

    /**
     * @return HandleMysqlDbLib
     */
    public static function getInstance() {
        return parent::getInstance();
    }

    public function __construct($modelName = null) {
        LogVendorLib::start(__CLASS__, __FUNCTION__);
        if (!$modelName) {
            $dataClassName = get_called_class();
            $modelName = preg_replace('/Data$/', 'DataModel', $dataClassName);
        }
        $model = new $modelName();
        $this->dataModel = $model;
        $this->modelName = $modelName;
        $this->tableName = $model->getTableName();
        LogVendorLib::end(__CLASS__, __FUNCTION__);
    }

    public function setDataModel($model) {
        if (!is_object($model)) {
            $model = new $model();
        }
        $this->dataModel = $model;
        $this->modelName = get_class($model);
        $this->tableName = $model->getTableName();
    }

    public function setTableName($tableName) {
        $this->tableName = $tableName;
        return $this;
    }
    
    /**
     * 表名
     * @return string
     */
    public function getTableName() {
        return $this->tableName;
    }

    public function useDb($dbname) {
        $sql = 'use ' . $dbname;
        $this->exec($sql);
    }
    
    /**
     * 选择数据库
     *
     * @param string $host
     *            主机地址
     * @param string $userName
     *            登陆名
     * @param string $passWord
     *            登陆密码
     * @param string $dbName
     *            数据库名
     * @param array $options
     *            PDO连接选项
     */
    public function selectDb($host, $userName, $passWord, $dbName, $port = 3306, $options = null) {
        LogVendorLib::start(__CLASS__, __FUNCTION__);
        try {
            $this->pdo = ConnectMysqlDbLib::connectMysql($host, $userName, $passWord, $dbName, $port, $options);
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
        if (!$this->pdo) {
            $this->selectDb(Config::DB_MYSQL_HOST, Config::DB_MYSQL_USERNAME, Config::DB_MYSQL_PASSWORD, Config::DB_MYSQL_DBNAME, Config::DB_MYSQL_PORT);
        }
        return $this->pdo;
        LogVendorLib::end(__CLASS__, __FUNCTION__);
    }

    public function escape($string) {
        return $string;
    }

    /**
     * 添加一个或者多个数据，返会的ID在model类中，多个则以数组形式array($dataMode1,$dataModel2);
     *
     * @param type $model            
     */
    public function add($model, $delayed = false) {
        LogVendorLib::start(__CLASS__, __FUNCTION__);
        $this->getDb();
        if (!is_array($model)) {
            $models = array(
                $model
            );
        } else {
            $models = $model;
        }
        // 得到预处理的sql与之绑定值的对应关系的数组
        $arrSql = $this->getPreSqlToVal($models, $delayed);
        foreach ($arrSql as $preSql => $arrVal) {
            try {
                $this->prepare($preSql);
                foreach ($arrVal as $k => $arr) {
                    $this->execute($arr);
                    $primaryKey = $models[$k]->getPrimaryKey();
                    if ($primaryKey && !$delayed) {
                        $id = $this->pdo->lastInsertId();
                        $models[$k]->$primaryKey = $id;
                    }
                }
            } catch (Exception $e) {
                $this->throwDbException($e);
            }
        }
        LogVendorLib::end(__CLASS__, __FUNCTION__);
    }

    public function prepare($sql) {
        LogVendorLib::dbStart(__CLASS__, __FUNCTION__, $sql);
        $this->getDb();
        try {
            $this->sth = $this->pdo->prepare($sql);
        } catch (Exception $e) {
            $this->throwDbException($e);
        }
        LogVendorLib::dbEnd(__CLASS__, __FUNCTION__);
    }

    public function execute($array = array()) {
        LogVendorLib::dbStart(__CLASS__, __FUNCTION__, $array);
        $this->clear();
        try {
            $this->sth->execute($array);
        } catch (Exception $e) {
            $this->throwDbException($e);
        }
        LogVendorLib::dbEnd(__CLASS__, __FUNCTION__);
    }

    public function exec($sql) {
        LogVendorLib::dbStart(__CLASS__, __FUNCTION__, $sql);
        $this->clear();
        $this->getDb();
        try {
            $result = $this->pdo->exec($sql);
        } catch (Exception $e) {
            $this->throwDbException($e);
        }
        LogVendorLib::dbEnd(__CLASS__, __FUNCTION__);
    }

    public function run($sql) {
        LogVendorLib::dbStart(__CLASS__, __FUNCTION__, $sql);
        $this->clear();
        $this->getDb();
        $statement = $this->pdo->query($sql);
        LogVendorLib::dbEnd(__CLASS__, __FUNCTION__);
        return $statement;
    }

    public function query($sql, $modelName = null) {
        LogVendorLib::dbStart(__CLASS__, __FUNCTION__, $sql);
        $this->getDb();
        try {
            if ($modelName) {
                $result = $this->pdo->query($sql)->fetchAll(PDO::FETCH_CLASS, $modelName);
            } else {
                $result = $this->pdo->query($sql, PDO::FETCH_ASSOC)->fetchAll();
            }
        } catch (Exception $e) {
            $this->throwDbException($e);
        }
        LogVendorLib::dbEnd(__CLASS__, __FUNCTION__);
        return $result;
    }

    public function queryOne($sql, $modelName = null) {
        LogVendorLib::dbStart(__CLASS__, __FUNCTION__, $sql);
        $this->getDb();
        try {
            if ($modelName) {
                $result = $this->pdo->query($sql)->fetchObject($modelName);
            } else {
                $result = $this->pdo->query($sql, PDO::FETCH_ASSOC)->fetch();
            }
        } catch (Exception $e) {
            $this->throwDbException($e);
        }
        LogVendorLib::dbEnd(__CLASS__, __FUNCTION__);
        return $result;
    }

    public function findAll() {
        LogVendorLib::start(__CLASS__, __FUNCTION__);
        $parameters = null;
        $where = $this->getWhereSql($parameters);
        $sql = 'select ' . $this->getLine() . ' from `' . $this->tableName . '`' .
                $where . $this->getOrder();
        if ($this->pageCore) {
            $pageCore = $this->pageCore;
            $countSql = 'select count(*) from `' . $this->tableName . '`' . $where;
            $this->prepare($countSql);
            $this->execute($parameters);
            $result = $this->sth->fetchAll(PDO::FETCH_COLUMN);
            $count = empty($result[0]) ? 0 : (int) $result[0];
            if (!$count) {
                return false;
            }
            $pageCore->count = $count;
            $pageCore->pageCount = ceil($count / $pageCore->pageSize);
            $this->setLimitByPageCore($pageCore);
        }
        $sql .= ' ' . $this->getLimit();
        $this->prepare($sql);
        $this->execute($parameters);
        $result = $this->sth->fetchAll(PDO::FETCH_CLASS, $this->modelName);
        LogVendorLib::end(__CLASS__, __FUNCTION__);
        return $result;
    }
    
    /**
     * 更新数据
     * @param type $data
     */
    public function updateData($data) {
        LogVendorLib::start(__CLASS__, __FUNCTION__);
        $parameters = null;
        $where = $this->getWhereSql($parameters);
        if (!$where) {
            $this->throwDbException(new Exception('没有修改条件'));
        }
        $parameters = !$parameters ? array() : $parameters;
        foreach ($data as $field => $val) {
            $preStr .= '`' . $field . '` = ?,';
            array_unshift($parameters, $val);
        }
        $preStr = trim($preStr, ',');
        $sql = 'update `' . $tableName . '` set ' . $preStr . $where;
        $this->prepare($sql);
        $this->execute($parameters);
        LogVendorLib::end(__CLASS__, __FUNCTION__);
    }

    public function updateModel($model) {
        LogVendorLib::start(__CLASS__, __FUNCTION__);
        $models = is_array($model) ? $model : array(
            $model
        );
        foreach ($models as $model) {
            $tableName = $model->getTableName();
            $workFields = $model->getWorkFields();
            $primaryKey = $model->getPrimaryKey();
            $preStr = '';
            $parameters = array();
            foreach ($workFields as $field) {
                $preStr .= '`' . $field . '` = ?,';
                $parameters[] = $model->$field;
            }
            $preStr = trim($preStr, ',');

            $parameters[] = $model->$primaryKey;
            $sql = 'update `' . $tableName . '` set ' . $preStr . ' where ' .
                    $primaryKey . ' = ?';
            $this->prepare($sql);
            $this->execute($parameters);
        }
        LogVendorLib::end(__CLASS__, __FUNCTION__);
    }

    public function findOne() {
        LogVendorLib::start(__CLASS__, __FUNCTION__);
        $parameters = null;
        $where = $this->getWhereSql($parameters);
        $sql = 'select ' . $this->getLine() . ' from `' . $this->tableName . '`' . $where . $this->getOrder() . ' limit 1';
        $this->prepare($sql);
        $this->execute($parameters);
        $result = $this->sth->fetchObject($this->modelName);
        LogVendorLib::end(__CLASS__, __FUNCTION__);
        return $result;
    }

    public function delete() {
        LogVendorLib::start(__CLASS__, __FUNCTION__);
        $parameters = null;
        $where = $this->getWhereSql($parameters);
        if (!$where) {
            $this->throwDbException(new Exception('没有删除条件'));
        }
        $sql = 'delete from `' . $this->tableName . '`' . $where;
        $this->exec($sql);
        LogVendorLib::end(__CLASS__, __FUNCTION__);
    }

    public function getOneById($id) {
        $query = array();
        $primaryKey = $this->dataModel->getPrimaryKey();
        $query[$primaryKey] = $id;
        return $this->where($query)->findOne();
    }

    public function delById($id) {
        LogVendorLib::start(__CLASS__, __FUNCTION__);
        $ids = is_array($id) ? $id : array(
            $id
        );
        if (!$ids) {
            return false;
        }
        $model = new $this->modelName();
        $primaryKey = $this->dataModel->getPrimaryKey();

        $preArr = array_pad(array(), count($ids), '?');
        $where = ' where ' . $primaryKey . ' in ';
        $where .= '(' . implode(',', $preArr) . ')';
        $sql = 'delete from `' . $this->tableName . '`' . $where;
        $this->prepare($sql);
        $this->execute($ids);
        LogVendorLib::end(__CLASS__, __FUNCTION__);
    }

    /**
     * 查询条件
     *
     * @param $input 传入需要查询的条件
     *            如：array(id>10)
     */
    public function where($input) {
        $parameters = func_get_args();
        foreach ($parameters as $key => $arr) {
            $i = 1;
            while ($i < count($parameters[$key])) {
                end($parameters[$key]);
                list ($k, $v) = each($parameters[$key]);
                $lastArr = array(
                    $k => $v
                );
                $last = array_pop($parameters[$key]);
                array_push($parameters, $lastArr);
            }
        }
        if ($this->whereTmp) {
            $this->whereTmp = array_merge($this->whereTmp, $input);
        } else {
            $this->whereTmp = $parameters;
        }
        return $this;
    }

    public function whereOr() {
        $this->where[] = $this->whereTmp;
        $this->whereTmp = array();
        return $this;
    }

    /**
     * 查询列条件
     *
     * @param $input 传入需要查询什么字段的信息            
     */
    public function setLine($input) {
        $fields = is_array($input) ? $input : array(
            $input
        );
        $this->line = array_merge($this->line, $fields);
        $this->line = array_unique($this->line);
        return $this;
    }

    public function setOrder($input) {
        $this->order = array_merge($this->order, $input);
        return $this;
    }

    public function setPage($pageCore) {
        $this->pageCore = $pageCore;
        return $this;
    }

    public function setLimitByPageCore($pageCore) {
        $limis = array();
        $offset = ($pageCore->currentPage - 1) * $pageCore->pageSize;
        $row = $pageCore->pageSize;
        $offset = $offset < 0 ? 0 : $offset;
        $row = $row < 0 ? 0 : $row;
        $this->setLimit(array(
            $offset,
            $row
        ));
    }

    /**
     * 设置查询limit
     *
     * @param
     *            $input
     */
    public function setLimit($input) {
        if (!$input) {
            return $this;
        }
        if (is_array($input) && count($input) == 2) {
            $this->limit = $input;
        } else
        if (is_numeric($input)) {
            $this->limit[] = 0;
            $this->limit[] = $input;
        }
        return $this;
    }

    private function getLimit() {
        $limit = '';
        if ($this->limit) {
            $limit = ' limit ' . (int) $this->limit[0] . ',' . $this->limit[1];
        }
        return $limit;
    }

    private function getOrder() {
        $order = '';
        if ($this->order) {
            $order = ' order by ';
            foreach ($this->order as $key => $val) {
                $order .= '`' . $key . '` ' . $val . ',';
            }
        }
        $order = trim($order, ',');
        return $order;
    }

    private function getLine() {
        $line = '*';
        if ($this->line) {
            $line = '`' . implode('`,`', $this->line) . '`';
        }
        return $line;
    }

    private function getWhereSql(&$parameters) {
        $parameters = array();
        if ($this->whereTmp) {
            $this->where[] = $this->whereTmp;
            $this->whereTmp = array();
        }
        $whereSql = '';
        $orSqlArr = array();
        foreach ($this->where as $or) {
            $andSqlArr = array();
            foreach ($or as $and) {

                $filed = key($and);
                $operator = '=';
                $value = current($and);

                if (is_array($value)) {
                    $current = current($value);

                    if (is_array($current)) {
                        $stra = '';
                        $operator = key($value);
                        $value = current($value);
                        foreach ($value as $v) {
                            $stra .= '?,';
                            $parameters[] = $v;
                        }
                        $stra = trim($stra, ',');
                        $andSqlArr[] = '(`' . $filed . '` ' . $operator . ' (' . $stra . '))';
                    } else {
                        $str = '(';
                        foreach ($value as $k => $v) {
                            $pre = '';
                            if ($str != '(') {
                                $pre = ' and ';
                            }
                            $str .= $pre . '`' . $filed . '` ' . $k . ' ?';
                            $parameters[] = $v;
                        }
                        $str .= ')';
                        $andSqlArr[] = $str;
                    }
                } else {
                    $andSqlArr[] = '(`' . $filed . '` ' . $operator . ' ?)';
                    $parameters[] = $value;
                }
            }
            $andSql = implode(' and ', $andSqlArr);
            if (count($or) > 1) {
                $andSql = '(' . $andSql . ')';
            }
            $orSqlArr[] = $andSql;
        }
        $whereSql = implode(' or ', $orSqlArr);
        if ($whereSql) {
            $whereSql = ' where ' . $whereSql;
        }
        return $whereSql;
    }

    private function clear() {
        $this->where = array();
        $this->whereTmp = array();
        $this->line = array();
        $this->order = array();
        $this->limit = array();
        $this->pageCore = null;
    }

    private function getPreSqlToVal($models, $delayed) {
        $arrSql = array();
        foreach ($models as $key => $model) {
            $tableName = $model->getTableName();
            $fileds = $model->getWorkFields($model);
            $arrVal = array_pad(array(), count($fileds), '?');

            $preSql = 'insert into ';
            if ($delayed) {
                $preSql = 'insert delayed into ';
            }
            $preSql .= '`' . $tableName . '` (`' . implode('`,`', $fileds) .
                    '`) values (' . implode(',', $arrVal) . ')';
            if (empty($arrSql[$preSql])) {
                $arrSql[$preSql] = array();
            }
            $preVal = array();
            foreach ($fileds as $filed) {
                $preVal[] = $model->$filed;
            }
            $arrSql[$preSql][$key] = $preVal;
        }
        return $arrSql;
    }

    private function throwDbException($e) {
        throw new DbExceptionLib($e->getMessage(), $e->getCode(), $e);
    }

}
