<?php
/**
 * Created by PhpStorm.
 * User: Qson
 * Date: 2019/10/15
 * Time: 14:49
 */
namespace MongodbLog\Model;
use MongodbLog\Helper\helper;
class ReadLog
{
    /**
     * @var $mongoManager \MongoDB\Driver\Manager
     */
    protected $mongoManager;
    protected $dbName;
    protected $lineNumList;
    protected $table;
    protected $baseLogPath;
    protected $host;
    protected $perReadLineNum;
    public function __construct($config)
    {
        $this->baseLogPath  = $config["base_log_path"];
        $this->dbName       = $config["db_name"];
        $this->table        = $config["table"];
        $this->host        = $config["host"];
        $this->perReadLineNum = $config["per_read_line_num"];
        if(!is_dir($this->baseLogPath)){
            throw new DirNotFound("Log root dir not found");
        }
        if(!is_writable($this->baseLogPath)){
            throw new DirNotFound("Log root dir not writeable");
        }
    }
    public function run($curDay=""){
        $this->mongoManager = new \MongoDB\Driver\Manager( $this->host);
        if(empty($curDay))
            $curDay = date("Y-m-d");
        $this->readDayLog($curDay,$this->table);
    }

    /**
     * @param $day
     * @param $table
     * 读取日志文件
     */
    protected  function readDayLog($day,$table){
        $basePath = $this->baseLogPath.$day."/$table/";
        if(!is_dir($basePath))return;
        $this->lineNumList = $this->getLineNumJson($basePath);
        $list = scandir($basePath);
        foreach ($list as $file){
            if(is_file($basePath.$file)){
                $fileName =  pathinfo($file,PATHINFO_FILENAME);
                if($fileName != "line_info"){
                    $num = 0;
                    if(isset($this->lineNumList[$fileName])){
                        $num = $this->lineNumList[$fileName];
                    }
                    $lineList = $this->readFile2arr($basePath.$file,$this->perReadLineNum ,$num);
                    $this->insertLogToMongo($fileName,$lineList,$table);
                }
            }
        }
        $this->writeLineJson($basePath);
    }

    /**
     * @param $basePath
     * @return array|mixed
     * 获取上次读取到的文件的行数
     */
    protected function getLineNumJson($basePath){
        $lineNumInfoFile = $basePath."/line_info.json";
        $lineNumList = [];
        if(is_file($lineNumInfoFile)){
            $jsonText = file_get_contents($lineNumInfoFile);
            $lineNumList = json_decode($jsonText,true);
        }
        return $lineNumList;
    }

    protected function writeLineJson($basePath){
        $lineNumInfoFile = $basePath."/line_info.json";
        file_put_contents($lineNumInfoFile,json_encode($this->lineNumList));
    }

    /**
     * @param $fileName
     * @param $lineList
     * @param $table
     * 插入数据到mongodb
     */
    protected function insertLogToMongo($fileName,$lineList,$table){
        if(empty($lineList))return;
        $bulk = new \MongoDB\Driver\BulkWrite;

        $absFileName = $fileName."_".date("Y-m-d");
        $hasData = 0;
        foreach ($lineList as $item){
            $jsonObj = json_decode($item,true);
            $jsonObj = Helper::replaceEmptyKey($jsonObj);
            if(!empty($jsonObj)){
                $jsonObj["file_name"] = $absFileName;
                try{
                    $bulk->insert($jsonObj);
                }catch (Exception $e){
                    $newData = [];
                    $newData["file_name"] = $absFileName;
                    $newData["data"] = $item;
                    $newData["type"] = "insert_error";
                    $newData["msg"] = $e->getMessage();
                    $bulk->insert($newData);
                }
                $hasData = 1;
            }
        }
        if(!$hasData){
            return ;
        }
        $table = $this->dbName.".".$table;
        $writeResult = $this->mongoManager->executeBulkWrite($table, $bulk);
        //实际插入mongodb的数据
        $insertCount = $writeResult->getInsertedCount();
        if(isset( $this->lineNumList[$fileName])){
            $this->lineNumList[$fileName] =  $this->lineNumList[$fileName] + $insertCount;
        }else{
            $this->lineNumList[$fileName] =  count($lineList);
        }

    }

    /**
     * @param $fileName
     * @param $table
     * @return mixed
     * @throws \MongoDB\Driver\Exception\Exception
     * 统计mongodb表中数据的数量
     */
    protected function getNumByName($fileName,$table){
        $absFileName = $fileName."_".date("Y-m-d");
        $command = new \MongoDB\Driver\Command([
            'count' => $table,
            'query' => ['file_name' => $absFileName],
            'maxTimeMS' => 1000,
        ]);
        $cursor = $this->mongoManager->executeCommand($this->dbName, $command);
        $result = $cursor->toArray()[0];
        if($result->ok == 1){
            return $result->n;
        }
    }

    /**
     * @param $path
     * @param $count
     * @param int $offset
     * @return array
     * 从文件指定行数读入数据到数组
     */
    protected function readFile2arr($path, $count, $offset=0) {
        $arr = array();
        if (! is_readable($path))
            return $arr;
        $fp = new \SplFileObject($path, 'r');
        // 定位到指定的行数开始读
        if ($offset)
            $fp->seek($offset);
        $i = 0;
        while (! $fp->eof()) {
            // 必须放在开头
            $i++;
            // 只读 $count 这么多行
            if ($i > $count)
                break;
            $line = $fp->current();
            $line = trim($line);
            $arr[] = $line;
            // 指向下一个，不能少
            $fp->next();
        }

        return $arr;
    }
}

