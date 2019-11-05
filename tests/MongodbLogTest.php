<?php
/**
 * Created by PhpStorm.
 * User: Qson
 * Date: 2019/11/5
 * Time: 11:41
 */

require_once "../src/MongodbLog.php";
require_once "../src/Application.php";
require_once "../src/Common/ReadLogToMongo.php";
require_once "../src/Common/WriteLog.php";
require_once "../src/Common/SearchMongoLog.php";
require_once "../src/Helper/Helper.php";

use MongodbLog\MongodbLog;

class MongodbLogTest
{
    public function __construct()
    {
        $config = [
            "base_log_path"=>"F:/www/mongodb/data/",
            "app"=>"index",
            "mod"=>"test",
            "db_name"=>"test2",
            'table'=>'mobile'
        ];
        MongodbLog::init($config);
        $this->testWriteLog();
        $this->testReadLog();
        $this->searchReadLog();
    }

    public function testWriteLog()
    {
        MongodbLog::writeLog("aaa",["bbb"]);
    }
    public function testReadLog()
    {
        MongodbLog::readLog();
    }
    public function searchReadLog()
    {
        $list = MongodbLog::search([],["pageSize"=>10,"currentPage"=>1]);
        print_r($list);
    }
}

$test = new MongodbLogTest();