<?php

class RuleUtil extends Feng {
    
    const PREG_EMAIL = '/^[a-zA-Z0-9_-]+@[a-zA-Z0-9_-]+(\.[a-zA-Z0-9_-]+)+$/';
    
    static function checkEmailFmt($email) {
        if (preg_match(self::PREG_EMAIL, $email)) {
            return true;
        }
        return false;
    }
}