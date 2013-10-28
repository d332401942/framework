<?php

class BusinessCoreLib extends Feng {

    public function throwException($msg = '', $code = 0) {
        throw new BusinessExceptionLib($msg, $code);
    }

}

?>
