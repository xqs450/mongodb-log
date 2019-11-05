<?php
/**
 * Created by PhpStorm.
 * User: Qson
 * Date: 2019/11/4
 * Time: 11:51
 */
namespace MongodbLog;

class MongodbLog
{
    /**
     * Internal factory storage
     *
     * @var Application
     */
    private static $app;
    private static $config;

    private static function getApp()
    {
        if (is_null(self::$app)) {
            self::$app = new Application(self::$config);
        }
        return self::$app;
    }

    public static function init($config){
        self::$config = $config;
    }

    public static function writeLog($title="",$data,$type="log",$module="mobile"){
        $app = self::getApp();
        $app->writeLog($title,$data,$type,$module);
    }

    public static function readLog(){
        $app = self::getApp();
        $app->readLog();
    }

    public static function search($condition,$pageInfo){
        $app = self::getApp();
        return $app->search($condition,$pageInfo);
    }
}