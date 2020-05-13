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
    protected $app;
    protected $mod;
    public function __construct($config){
        $this->baseLogPath = $config["base_log_path"];
        if(!is_dir($this->baseLogPath)){
            throw new DirNotFound("Log root dir not found");
        }
        if(!is_writable($this->baseLogPath)){
            throw new DirNotFound("Log root dir not writeable");
        }
    }

    /**
     * @param $app
     * @param $mod
     * 设置请求的路由名和方法名
     */
    public function setRouter($app,$mod){
        $this->app = $app;
        $this->mod = $mod;
    }

    /**
     * @param string $title 标题名称
     * @param array $data 日志数据
     * @param string $type 日志类型
     * @param string $module 日志模块
     * @return bool|int
     * 写日志
     */
    public function writeLog($title="",$data=[],$type="log",$module="mobile"){
        $curTime = time();
        if(!isset($GLOBALS["is_write_fist"])){
            $reg                 = $_REQUEST;
            $reg ["time"]        = $curTime;
            $reg ["random"]      = rand(1000,9999);
            $unionSessionId      = md5(json_encode($reg));
            $GLOBALS["is_write_fist"] = 1;
            $GLOBALS["union_session_id"] = $unionSessionId;
        }else{
            $unionSessionId = $GLOBALS["union_session_id"];
        }
        $baseLogPath = $this->baseLogPath .date("Y-m-d")."/";
        if(!is_dir($baseLogPath)){
            mkdir($baseLogPath);
        }
        $uid            = 0;
        if(isset($GLOBALS["member_id"])){
            $uid = $GLOBALS["member_id"];
        }
        $query = $_REQUEST;

        $fileName = trim($this->app."-".$this->mod);
        $dataObj = [];
        $dataObj["union_id"]   = $unionSessionId;
        $dataObj["data"]        = $data;
        $dataObj["type"]        = $type;
        $dataObj["title"]       = $title;
        $dataObj["time"]        = $curTime;
        $dataObj["uid"]         = $uid;
        $dataObj["query"]       = $query;
        $dataObj["app"]         = trim($this->app);
        $dataObj["mod"]         = trim($this->mod);
        $dataObj["client_ip"]   = Helper::getClientIpAddress();

        $baseLogModulePath = $this->baseLogPath.date("Y-m-d")."/".$module."/";
        if(!is_dir($baseLogModulePath)){
            mkdir($baseLogModulePath);
        }
        $absFile = $baseLogModulePath.$fileName.".log";
        $str = json_encode($dataObj) . PHP_EOL;
        return file_put_contents($absFile, $str, FILE_APPEND);
    }
}