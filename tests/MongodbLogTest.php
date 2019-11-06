<?php
/**
 * Created by PhpStorm.
 * User: Qson
 * Date: 2019/11/5
 * Time: 11:41
 */

require_once "../src/autoload.php";

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
        $this->searchLog();
    }

    public function testWriteLog()
    {
        MongodbLog::writeLog("aaa",["bbb"]);
    }
    public function testReadLog()
    {
        MongodbLog::readLog();
    }
    public function searchLog()
    {
        $list = MongodbLog::search(["sh_app"=>"index"],["pageSize"=>10,"currentPage"=>1]);
        print_r($list);
    }
}

$test = new MongodbLogTest();