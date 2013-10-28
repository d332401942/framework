<?php

class MailModelLib extends Feng {

    /**
     * 收件人邮箱
     * @var type 
     */
    public $To;

    /**
     * 发件人
     */
    public $From;

    /**
     * 邮件主题
     * @var type 
     */
    public $Subject;

    /**
     * 邮件内容
     * @var type 
     */
    public $Content;

    /**
     * 抄送
     * @var type 
     */
    public $Cc;

    /**
     * 密送
     * @var type 
     */
    public $Bcc;

}
