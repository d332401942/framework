<?php

class OtherExceptionLib extends Exception {

    const CLOSE_WINDOW = 'closewindow';

    public function __construct($message = '', $code = 0, $previous = null) {
        parent::__construct($message, $code, $previous);
    }

}
