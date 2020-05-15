<?php
/**
 * Created by PhpStorm.
 * User: Qson
 * Date: 2019/11/4
 * Time: 15:26
 */

namespace MongodbLog\Model;
use MongodbLog\Exception\DirNotFound;
use MongodbLog\Helper\helper;

class WriteLog
{
    protected $baseLogPath;
    protected $method;
    protected $responseData;
    protected $takeUpTime;
    protected $unionSessionId;
    protected $userId;
    protected $uri;
    public function __construct($config){
        $this->baseLogPath = $config["base_log_path"];
        if(!is_dir($this->baseLogPath)){
            throw new DirNotFound("Log root dir not found");
        }
        if(!is_writable($this->baseLogPath)){
            throw new DirNotFound("Log root dir not writeable");
        }
        $this->userId = 0;
    }

    public function setRouter($uri,$method){
        $this->uri      = $uri;
        $this->method   = $method;
        $this->unionSessionId = $this->makeUnionSessionId();
    }

    public function setResponseData($data){
        $this->responseData = $data;
    }

    public function setTakeUpTime($takeUpTime){
        $this->takeUpTime = $takeUpTime;
    }

    public function setUserId($userId){
        $this->userId = $userId;
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
    public function writeLog($title="",$data=[],$type="log",$module="mobile",$isDetail=true){
        $curTime = time();
        $baseLogPath = $this->baseLogPath .date("Y-m-d")."/";
        if(!is_dir($baseLogPath)){
            mkdir($baseLogPath);
        }
        $dataObj = [];
        $dataObj["union_id"]    = $this->unionSessionId;
        $dataObj["data"]        = $data;
        $dataObj["type"]        = $type;
        $dataObj["title"]       = $title;
        $dataObj["time"]        = intval($curTime);
        $dataObj["user_id"]     = $this->userId;
        $dataObj["method"]      = trim($this->method);
        $dataObj["uri"]         = trim($this->uri);
        if($isDetail){
            $dataObj["query"]       = $_REQUEST;
            $dataObj["client_ip"]   = Helper::getClientIpAddress();
            if(isset($this->responseData)){
                $dataObj["response_data"] = $this->responseData;
            }
            if(isset($this->takeUpTime)){
                $dataObj["take_up_time"] = intval($this->takeUpTime);
            }
        }
        $fileName = trim(str_replace("/","_",$this->uri));
        $baseLogModulePath = $this->baseLogPath.date("Y-m-d")."/".$module."/";
        if(!is_dir($baseLogModulePath)){
            mkdir($baseLogModulePath);
        }
        $absFile = $baseLogModulePath.$fileName.".log";
        $str = json_encode($dataObj) . PHP_EOL;
        return file_put_contents($absFile, $str, FILE_APPEND);
    }

    /**
     * @return string
     * 为每个请求生成唯一id
     */
    protected function makeUnionSessionId(){
        $curTime = time();
        $reg                 = $_REQUEST;
        $reg ["time"]        = $curTime;
        $reg ["random"]      = rand(1000,9999);
        $unionSessionId      = md5(json_encode($reg));
        return $unionSessionId;
    }
}