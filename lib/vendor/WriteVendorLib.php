<?php

class WriteVendorLib extends Feng {

    private static $self;
    private $handles = array();

    private function __construct() {
        ;
    }

    public static function getMy() {
        if (!self::$self) {
            self::$self = new WriteVendorLib();
        }
        return self::$self;
    }

    public function write($file, $content) {
        if (!file_exists(dirname($file))) {
            mkdir(dirname($file));
        }
        if (empty($this->handles[$file])) {
            $this->handles[$file] = fopen($file, 'a');
        }

        fwrite($this->handles[$file], $content . "\n");
    }

    public function __destruct() {
        foreach ($this->handles as $handle) {
            fclose($handle);
        }
    }

}
