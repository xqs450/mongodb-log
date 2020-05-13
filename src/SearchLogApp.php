<?php

namespace MongodbLog;
use MongodbLog\Model\SearchLog;

/**
 * Created by PhpStorm.
 * User: Qson
 * Date: 2020/5/13
 * Time: 11:23
 */

class SearchLogApp
{

    /**
     * @var $searchLog SearchLog
     */
    private static $searchLog;
    public static function init($config)
    {
        if (self::$searchLog == null) {
            self::$searchLog = new SearchLog($config);
        }
    }

    /**
     * @param $condition array
     * @param $pageInfo array currentPage当前第几页 pageSize每页的数量
     * @return array
     */
    public static function search($condition=[],$pageInfo=[])
    {
        return self::$searchLog->search($condition,$pageInfo);
    }
}