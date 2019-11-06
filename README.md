# MongodbLog PHP library

The MongodbLog PHP library storing logs in mongodb for easy query and analysis

## Dependencies

PHP version >= 5.4.0 is required.

The following PHP extensions are required:

* mongodb

## Quick Start Example

```php
<?php

require_once 'PATH_TO_BRAINTREE/src/autoload.php';

// 初始化MongodbLog 类，传入存储日志的路径，模块名称，方法名称及mongodb中创建的数据库名及集合名称
 $config = [
            "base_log_path"=>"your log file path",
            "app"=>"your controller name ",
            "mod"=>"your method name",
            "db_name"=>"your mongodb db name",
            'table'=>'your mongodb collection name'
        ];
        MongodbLog::init($config);

// 日志写入文件代码:传入标题，日志内容和日志模块自定义类型
 MongodbLog::writeLog("title",["data"],"log");
 
 //日志读取入库类，分片读取日志入库到mongodb，后台需要起一个进程不断读取或者定时任务读取
 MongodbLog::readLog();
 
 //后台根据日志类型查询日志接口:例如查询控制器为index的类型
 $list = MongodbLog::search(["sh_app"=>"index"],["pageSize"=>10,"currentPage"=>1]);
```

## php ini


```ini
extension = "mongo.so"
```


## Testing
php tests/MongodbLogTest.php
```

## License

See the LICENSE file.
