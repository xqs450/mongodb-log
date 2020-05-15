<?php
/**
 * Created by PhpStorm.
 * User: Qson
 * Date: 2020/5/13
 * Time: 11:12
 */

namespace MongodbLog;


use MongodbLog\Model\ReadLog;

class ReadLogApp
{
    /**
     * @var $writeLog ReadLog
     */
    private static $readLog;

    public static function init($config)
    {
        if (self::$readLog == null) {
            self::$readLog = new ReadLog($config);
        }
    }
    public static function run()
    {
        if(!self::$readLog){
            return false;
        }
        self::$readLog->run();
    }
}