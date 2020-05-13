<?php
/**
 * Created by PhpStorm.
 * User: Qson
 * Date: 2020/5/13
 * Time: 11:12
 */

namespace MongodbLog;


use MongodbLog\Model\WriteLog;

class WriteLogApp
{
    /**
     * @var $writeLog WriteLog
     */
    private static $writeLog;
    public  static  function init($config){
        if(self::$writeLog == null){
            self::$writeLog = new WriteLog($config);
        }
    }
    public static function  setRouter($app,$mod){
        self::$writeLog->setRouter($app,$mod);
    }

    /**
     * @param string $title 日志标题
     * @param array $data 日志数据
     * @param string $type 日志类型
     * @param string $module 日志所属模块
     */
    public static function  writeLog($title="",$data=[],$type="log",$module="mobile"){
        self::$writeLog->writeLog($title,$data,$type,$module);
    }
}