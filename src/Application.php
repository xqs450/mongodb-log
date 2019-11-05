<?php
/**
 * Created by PhpStorm.
 * User: Qson
 * Date: 2019/11/4
 * Time: 15:49
 */

namespace MongodbLog;
use MongodbLog\Common\WriteLog;
use MongodbLog\Common\ReadLogToMongo;
use MongodbLog\Common\SearchMongoLog;

class Application
{
    protected $config;
    protected $writeLog;
    protected $readLog;
    protected $searchLog;


    public function __construct($config)
    {
        $this->config = $config;
    }
    public function writeLog($title="",$data,$type="log",$module="mobile"){
        if(!$this->writeLog){
            $this->writeLog = new WriteLog($this->config);
        }
        $this->writeLog ->writeLog($title,$data,$type,$module);
    }
    public function readLog(){
        if(!$this->readLog){
            $this->readLog = new ReadLogToMongo($this->config);
        }
        $this->readLog->run();
    }
    public function search($condition,$pageInfo){
        if(!$this->searchLog){
            $this->searchLog = new SearchMongoLog($this->config);
        }
        return $this->searchLog->search($condition,$pageInfo);
    }
}