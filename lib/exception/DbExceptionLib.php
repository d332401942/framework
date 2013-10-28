<?php

class DbExceptionLib extends Exception {

    public function __construct($message = '', $code = 0, $previous = null) {
        parent::__construct($message, (int) $code, $previous);
    }

}
