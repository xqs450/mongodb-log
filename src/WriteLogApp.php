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
     * @param $control
     * @param $method
     * 设置请求的路由名和方法名
     */
    public static function  setRouter($control,$method){
        self::$writeLog->setRouter($control,$method);
    }
    /**
     * @param $data
     * 设置用户返回数据
     */
    public static function  setReturnData($data){
        self::$writeLog->setReturnData($data);
    }
    /**
     * @param $takeUpTime
     * 设置当前请求所使用时间
     */
    public static function  setTakeUpTime($takeUpTime){
        self::$writeLog->setTakeUpTime($takeUpTime);
    }

    public static function setUserId($userId){
        self::$writeLog->setUserId($userId);
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
        self::$writeLog->writeLog($title,$data,$type,$module,$isDetail);
    }
}