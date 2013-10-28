<?php

class ConnectMysqlDbLib extends Feng {

    private static $pdo = array();

    private function __construct() {
        
    }

    public static function connectMysql($host, $userName, $passWord, $dbName, $port = 3306, $options = null) {
        if (empty(self::$pdo[$host][$dbName])) {
            LogVendorLib::start(__CLASS__, __FUNCTION__);
            $dsn = 'mysql:dbname=' . $dbName . ';host=' . $host . ';port=' . $port;
            self::$pdo[$host][$dbName] = new PDO($dsn, $userName, $passWord, $options);
            self::$pdo[$host][$dbName]->setAttribute(
                    PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_CLASS);
            self::$pdo[$host][$dbName]->setAttribute(
                    PDO::ATTR_STRINGIFY_FETCHES, FALSE);
            self::$pdo[$host][$dbName]->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$pdo[$host][$dbName]->query('set names utf8');
            LogVendorLib::end(__CLASS__, __FUNCTION__);
        }
        return self::$pdo[$host][$dbName];
    }

}
