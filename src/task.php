<?php
/**
 * Created by PhpStorm.
 * User: Qson
 * Date: 2019/11/9
 * Time: 10:53
 */
require_once (__DIR__ . DIRECTORY_SEPARATOR.'autoload.php');
//每八秒钟跑一次入库程序。每十分钟重启一次
use MongodbLog\MongodbLog;

$config = [
    "base_log_path"=>"F:/www/mongodb/data/",
    "db_name"=>"test2",
    'table'=>'mobile'
];
MongodbLog::init($config);

$curTime = time();
while (true){
    MongodbLog::readLog();
    usleep(8000000);
    if(time() - $curTime > 60*10){
        exit();
    }
}
