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
    /**
     * @param $uri
     * @param $method
     * 设置请求的uri和方法名
     */
    public static function  setRouter($uri,$method){
        if(!self::$writeLog){
            return false;
        }
        self::$writeLog->setRouter($uri,$method);
        return true;
    }
    /**
     * @param $data
     * 设置接口响应返回数据
     */
    public static function  setResponseData($data){
        if(!self::$writeLog){
            return false;
        }
        self::$writeLog->setResponseData($data);
        return true;
    }
    /**
     * @param $takeUpTime
     * 设置当前请求所使用时间
     */
    public static function  setTakeUpTime($takeUpTime){
        if(!self::$writeLog){
            return false;
        }
        self::$writeLog->setTakeUpTime($takeUpTime);
        return true;
    }

    public static function setUserId($userId){
        if(!self::$writeLog){
            return false;
        }
        self::$writeLog->setUserId($userId);
        return true;
    }

    /**
     * @param string $title 标题名称
     * @param array $data 日志数据
     * @param string $type 日志类型
     * @param string $module 日志模块
     * @param bool $isDetail 是否是详情数据，详情数据会记录请求数据和返回数据
     * @return bool|int
     * 写日志
     */
    public static function writeLog($title="",$data=[],$type="log",$module="mobile",$isDetail=true){
        if(!self::$writeLog){
            return false;
        }
        return self::$writeLog->writeLog($title,$data,$type,$module,$isDetail);
    }
}