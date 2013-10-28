<?php

class UserDataModel extends BaseDataModel {

    /**
     * 登陆邮箱
     * @var string
     */
    public $Email;

    /**
     * 密码
     * @var string
     */
    public $Password;

    /**
     * 昵称
     * @var string
     */
    public $NickName;

    /**
     * 头像
     * @var string
     */
    public $Picture;

    /**
     * 性别
     * @var int
     */
    public $Sex;

    /**
     * 生日
     * @var int
     */
    public $Birthday;

    /**
     * 希望发展城市 多个用逗号隔开
     * @var string
     */
    public $Sites;

    /**
     * 希望发展行业，多个用逗号隔开
     * @var string
     */
    public $Industries;

    /**
     * 个性签名
     * @var string
     */
    public $Signature;

    /**
     * 注册时间
     * @var int
     */
    public $CreateTime;

    /**
     * 资料修改时间
     * @var int
     */
    public $UpdateTime;

    /**
     * 最后登陆Ip
     * @var string
     */
    public $LastLoginIp;

    /**
     * 用户状态
     * @var int
     */
    public $Status;

}
