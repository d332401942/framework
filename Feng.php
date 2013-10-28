<?php

class Feng {

    private static $class = array();

    public static function getInstance() {
        $className = get_called_class();
        if (empty(self::$class[$className])) {
            self::$class[$className] = new $className();
        }
        return self::$class[$className];
    }

    public static function sysExit() {
        throw new Exception($message, $code, $previous);
    }

}
