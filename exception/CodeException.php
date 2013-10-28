<?php

/**
 * 错误信息与错误代码 
 */
class CodeException extends Feng {
    
    /**
     * email 错误  email格式错误或者没有填写等 
     */
    const USER_EMAIL_ERROR = '10001';
    
    const USER_PASSWORD_EMPTY = '10002';
    
    const USER_NOT_LOGIN = '10003';
}