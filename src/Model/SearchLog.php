<?php
/**
 * Created by PhpStorm.
 * User: Qson
 * Date: 2019/11/4
 * Time: 15:04
 */

namespace MongodbLog\Model;

class SearchLog
{
    protected $mongoManager;
    protected $dbName;
    protected $lineNumList;
    protected $table;
    protected $host;
    public function __construct($config)
    {
        $this->dbName       = $config["db_name"];
        $this->table        = $config["table"];
        $this->host         = $config["host"];
    }
    public function search($condition,$pageInfo){
        $filter = new \stdClass();
        $search = array();
        if(isset($condition["sh_mod"])){
            $filter->mod = $condition["sh_mod"];
            $search['sh_mod'] = $condition["sh_mod"];
        }
        if(isset($condition["sh_app"])){
            $filter->app = $condition["sh_app"];
            $search['sh_app'] = $condition["sh_app"];
        }
        if(isset($condition["start_time"]) && isset($condition["end_time"])){
            $startTime = strtotime($condition["start_time"]);
            $endTime = strtotime($condition["end_time"]);
            $filter->time = ['$gt' =>$startTime,'$lt'=>$endTime];
            $search['start_time'] = $condition["start_time"];
            $search['end_time'] = $condition["end_time"];
        }else if(isset($condition["start_time"])){
            $startTime = strtotime($condition["start_time"]);
            $filter->time   = ['$gt' => $startTime];
            $search['start_time'] = $condition["start_time"];
        }else if(isset($condition["end_time"])){
            $endTime = strtotime($condition["end_time"]);
            $filter->time   = ['$lt'=>$endTime];
            $search['end_time'] = $condition["end_time"];
        }
        if(isset($condition["user_id"])){
            $filter->uid = $condition["user_id"];
            $search['user_id'] = $condition["user_id"];
        }
        if(isset($condition["client_ip"])){
            $filter->client_ip = $condition["client_ip"];
            $search['client_ip'] = $condition["client_ip"];
        }
        if(isset($condition["type"])){
            $filter->type = $condition["type"];
            $search['type'] = $condition["type"];
        }
        if(isset($condition["title"])){
            $filter->title = $condition["title"];
            $search['title'] = $condition["title"];
        }
        if(isset($condition["union_id"])){
            $filter->union_id = $condition["union_id"];
            $search['union_id'] = $condition["union_id"];
        }
        $manager    = new \MongoDB\Driver\Manager($this->host);
        $command = new \MongoDB\Driver\Command([
            'count' => $this->table,
            'query' => $filter,
            'maxTimeMS' => 1000,
        ]);
        $cursor = $manager->executeCommand($this->dbName, $command);
        $result = $cursor->toArray()[0];

        $pageSize = intval($pageInfo['pageSize'] > 0 ? $pageInfo['pageSize'] : 10);
        $page = intval($pageInfo['currentPage'] > 0 ? $pageInfo['currentPage'] : 1);

        $total = 0;
        $list = [];
        if($result->ok == 1){
            $total = $result->n;
            $skip = ($page-1)*$pageSize;
            $options    = [
                'projection'    => ['_id' => 0],
                'sort'          => ['time' => -1],
                'skip'          => $skip,
                'limit'         => $pageSize,
            ];
            $query = new \MongoDB\Driver\Query($filter, $options);
            $list = $manager->executeQuery($this->dbName.".".$this->table, $query);
            $list = $list->toArray();
        }
        foreach($list as &$item){
            $item->time = date("Y-m-d H:i:s",$item->time);
        }
        $return_last = array(
            'list' => $list,
            'pagination' => array(
                'current' => $page,
                'pageSize' => $pageSize,
                'total' => $total,
            ),
            'searchlist'=>$search
        );
        return $return_last;
    }
}