<?php
/**
 * Created by PhpStorm.
 * User: Qson
 * Date: 2019/10/15
 * Time: 14:49
 */
namespace MongodbLog\Common;
use MongodbLog\Helper\helper;
class ReadLogToMongo
{
    protected $mongoManager;
    protected $dbName;
    protected $lineNumList;
    protected $table;
    protected $baseLogPath;
    public function __construct($config)
    {
        $this->baseLogPath  = $config["base_log_path"];
        $this->dbName       = $config["db_name"];
        $this->table        = $config["table"];
    }
    public function run($curDay=""){
        $this->mongoManager = new \MongoDB\Driver\Manager("mongodb://localhost:27017");
        if(empty($curDay))
            $curDay = date("Y-m-d");
        $this->readDayLog($curDay,$this->table);
    }


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
                    $lineList = $this->readFile2arr($basePath.$file,1000,$num);
                    $this->insertLogToMongo($fileName,$lineList,$table);
                }
            }
        }
        $this->writeLineJson($basePath);
    }

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
        $this->mongoManager->executeBulkWrite($table, $bulk);
        if(isset( $this->lineNumList[$fileName])){
            $this->lineNumList[$fileName] =  $this->lineNumList[$fileName] + count($lineList);
        }else{
            $this->lineNumList[$fileName] =  count($lineList);
        }

    }

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
        exit("读取出错");
    }
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

