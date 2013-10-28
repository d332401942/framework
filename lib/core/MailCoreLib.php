<?php

class MailCoreLib extends Feng {

    private $server;
    private $port;
    private $username;
    private $password;
    private $link;
    private $mailModel;
    private static $tn = "\r\n";

    public function __construct() {
        $this->server = Config::EMAIL_SMTP_SERVER;
        $this->port = Config::EMAIL_SMTP_PORT ? Config::EMAIL_SMTP_PORT : 25;
        $this->username = Config::EMAIL_SMTP_USERNAME;
        $this->password = Config::EMAIL_SMTP_PASSWORD;
    }

    public function sendMail($mailModel) {
        $mailModel->From = Config::EMAIL_SMTP_USERNAME;
        if (!$mailModel->To) {
            throw new MailExceptionLib('收件人必须填写');
        }
        if (!$mailModel->From) {
            $mailModel->From = Config::EMAIL_SMTP_USERNAME;
        }
        $this->mailModel = $mailModel;
        $toArray = is_array($mailModel->To) ? $mailModel->To : array(
            $mailModel->To
        );
        foreach ($toArray as $email) {
            $this->connect($email);
            $this->send($toArray);
        }
    }

    private function connect($email) {
        $this->link = fsockopen($this->server, $this->port);
        if (!$this->link) {
            throw new MailExceptionLib('邮件服务器连接失败');
        }
        // stream_set_blocking($link, true);
        $lastmessage = fgets($this->link, 512);
        if (!preg_match('/^220/', $lastmessage)) {
            throw new MailExceptionLib('邮件服务器连接失败');
        }
        fputs($this->link, "HELO phpsetmail" . self::$tn);
        $lastmessage = fgets($this->link, 2000);
        if (!preg_match('/^250/', $lastmessage)) {
            throw new MailExceptionLib('与服务器HELO失败');
        }
        fputs($this->link, 'AUTH LOGIN' . self::$tn);
        $lastmessage = fgets($this->link, 2000);
        if (!preg_match('/^334/', $lastmessage)) {
            throw new MailExceptionLib('请求与服务器进行用户验证失败');
        }
        fputs($this->link, base64_encode($this->username) . self::$tn);
        $lastmessage = fgets($this->link, 2000);
        if (!preg_match('/^334/', $lastmessage)) {
            throw new MailExceptionLib('请求与服务器进行用户验证失败');
        }
        fputs($this->link, base64_encode($this->password) . self::$tn);
        $lastmessage = fgets($this->link, 2000);
        if (!preg_match('/^235/', $lastmessage)) {
            throw new MailExceptionLib('请求与服务器进行用户验证失败');
        }
        fputs($this->link, 'MAIL From: <' . $this->mailModel->From . '>' . self::$tn);
        $lastmessage = fgets($this->link, 2000);
        if (!preg_match('/^250/', $lastmessage)) {
            throw new Exception('与服务器MAIL FROM失败');
        }
        fputs($this->link, 'RCPT TO: <' . $email . '>' . self::$tn);
        $lastmessage = fgets($this->link, 2000);
        if (!preg_match('/^250/', $lastmessage)) {
            throw new Exception('与服务器RCPI TO失败');
        }
    }

    private function send($toArray) {
        fputs($this->link, 'DATA' . self::$tn);

        $lastmessage = fgets($this->link, 2000);

        if (!preg_match('/^354/', $lastmessage)) {
            throw new ErrorException('请求与服务器发送邮件数据失败');
        }
        // echo $lastmessage; //是不是要回应才能发呢？
        fputs($this->link, 'Content-Type: text/html; charset=utf-8' . self::$tn);
        fputs($this->link, 'From:' . $this->mailModel->From . self::$tn);
        fputs($this->link, 'Subject:' . $this->mailModel->Subject . self::$tn);
        fputs($this->link, 'To:' . implode(',', $toArray) . self::$tn);
        fputs($this->link, self::$tn);
        fputs($this->link, $this->mailModel->Content . self::$tn);
        fputs($this->link, '.' . self::$tn);
        fputs($this->link, 'QUIT' . self::$tn);
        $lastmessage = fgets($this->link, 2000);
        echo $lastmessage;
        fclose($this->link);
    }

}
