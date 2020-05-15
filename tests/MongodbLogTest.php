<?php
/**
 * Created by PhpStorm.
 * User: Qson
 * Date: 2019/11/5
 * Time: 11:41
 */

require_once "./autoload.php";

use \MongodbLog\WriteLogApp;
use \MongodbLog\ReadLogApp;
use \MongodbLog\SearchLogApp;
use PHPUnit\Framework\TestCase;

class MongodbLogTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $config = [
            "base_log_path"=>"F:/www/mongodb/data/",
            "db_name"=>"test2",
            'table'=>'mobile',
            'host'=>'mongodb://localhost:27017',
            'per_read_line_num'=>1000
        ];
        WriteLogApp::init($config);
        SearchLogApp::init($config);
        ReadLogApp::init($config);
    }

    public function testWriteLog()
    {
        WriteLogApp::setRouter("/index/index","get");
        WriteLogApp::writeLog("aaa",["bbb"]);
        $this->assertTrue(true);
    }
    public function testReadLog()
    {
        ReadLogApp::run();
        $this->assertTrue(true);
    }
    public function testSearchLog()
    {
        $list = SearchLogApp::search(["control"=>"index"],["pageSize"=>10,"currentPage"=>1]);
        $this->assertTrue(true);
    }
}