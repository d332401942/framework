<?php

class Conf extends Feng {

    const VIEW_FOLDER = 'view';

    /**
     * 默认访问试图的文件名
     */
    const VIEW_FILE = 'index';

    /**
     * 视图层文件夹
     */
    const VIEW_DOLDER = 'index';

    /**
     * URL与get参数分割符号
     */
    const URL_LIMIT_GET = '__';

    /**
     * 默认调用视图方法
     */
    const VIEW_FUNC = 'index';

    /**
     * 模版文件夹
     */
    const TEMPLATE_DOLDER = 'template';

    /**
     * 编译文件夹
     */
    const COMPILE_DOLDER = 'compile';

    /**
     * 模版文件后缀
     */
    const TEMPLATE_FILE_TYPE = 'html';

    /**
     * 模版左边定界符号
     */
    const COMPILE_RIGHT_LEFT = '<{';

    /**
     * 模版右边定界符号
     */
    const COMPILE_RIGHT_LIMIT = '}>';
    const RESOURCE_DOLDER = 'resource';

    /**
     * 模版过期时间
     */
    const COMPILE_FILE_LIFE_TIME = 0;

    /**
     * mysql主机地址
     */
    const DB_MYSQL_HOST = '';

    /**
     * mysql 用户名称
     */
    const DB_MYSQL_USERNAME = '';

    /**
     * mysql 密码
     */
    const DB_MYSQL_PASSWORD = '';

    /**
     * 数据库名称
     */
    const DB_MYSQL_DBNAME = '';

    /**
     * 数据库表前缀
     */
    const DB_PRE_TABLENAME = '';

    /**
     * 数据库表后缀
     */
    const DB_LAST_TABLENAME = '';

    /**
     * redis 地址
     */
    const REDIS_HOST = '';

    /**
     * redis 端口
     */
    const REDIS_PORT = '';

    /**
     * redis 密码
     */
    const REDIS_PASSWORD = '';

    /**
     * mongodb 连接字符串
     */
    const MONGO_STRING = 'mongodb://root:123456@127.0.0.1:27017';
    const MONGO_DBNAME = 'File';

    /**
     * 是否开启运行日志记录
     */
    const LOG_RUN_IS_OPEN = true;

    /**
     * 日志文件夹名称
     */
    const LOG_DOLDER = 'log';
    const FIRE_DEBUG = true;
    const DEBUG = true;

    /** Email设置 * */

    /**
     * SMTP服务地址
     */
    const EMAIL_SMTP_SERVER = '';
    const EMAIL_SMTP_PORT = '';
    const EMAIL_SMTP_USERNAME = '';
    const EMAIL_SMTP_PASSWORD = '';

}
