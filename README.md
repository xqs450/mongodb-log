# MongodbLog PHP library

The MongodbLog PHP library storing logs in mongodb for easy query and analysis
一款基于mongodb的简洁的，高性能的php日志写入，收集，查询扩展库，可用于日志收集，错误上报及性能分析等。
1.采用json存储写入的日志
2.采用SplFileObject类读取文件，支持分页读取文件。结合supervisor可准时候采集数据。
3.简单封装mongodb查询接口

## Dependencies

PHP version >= 5.4.0 is required.

The following PHP extensions are required:

* mongodb

## install
composer require qson/mongodb

## Quick Start Example
写入日志Demo
```php
<?php

use MongodbLog\WriteLogApp;

 $config = [
            "base_log_path"=>"F:/www/mongodb/data/", //日志存储目录
            "db_name"=>"test2", //mongodb数据库名称
            'table'=>'mobile',//mongdb 表名称
            'host'=>'mongodb://localhost:27017', //mongodb连接地址
            'per_read_line_num'=>1000 //每次读取行数
        ];
 WriteLogApp::init($config); //设置初始化配置文件
 WriteLogApp::setRouter("index","index");//记录访问的路由名称和方法名称
 WriteLogApp::writeLog("aaa",["bbb"]); //写入日志
        
```

读取日志Demo，参照task，实际使用需要配置读取进程，循环读取
```php
<?php

use MongodbLog\ReadLogApp;

 $config = [
            "base_log_path"=>"F:/www/mongodb/data/", //日志存储目录
            "db_name"=>"test2", //mongodb数据库名称
            'table'=>'mobile',//mongdb 表名称
            'host'=>'mongodb://localhost:27017', //mongodb连接地址
            'per_read_line_num'=>1000 //每次读取行数
        ];
 ReadLogApp::init($config); //设置初始化配置文件
 ReadLogApp::run();
        
```

查询demo
```php
<?php

use MongodbLog\SearchLogApp;

 $config = [
            "base_log_path"=>"F:/www/mongodb/data/", //日志存储目录
            "db_name"=>"test2", //mongodb数据库名称
            'table'=>'mobile',//mongdb 表名称
            'host'=>'mongodb://localhost:27017', //mongodb连接地址
            'per_read_line_num'=>1000 //每次读取行数
        ];
 SearchLogApp::init($config); //设置初始化配置文件
 $list = SearchLogApp::search(["sh_app"=>"index"],["pageSize"=>10,"currentPage"=>1]);        
```

## php ini
```ini
extension = "mongodb.so"
```


## Testing
```
phpunit MongodbLogTest.php
```
## Mongodb
```
1.Mongdob 对内存占用比较多，需要对其进行限制,修改/etc/mongod.conf文件。添加cacheSizeGB限制。
# Where and how to store data.
storage:
  dbPath: /var/lib/mongodb
  journal:
    enabled: true

  wiredTiger:
    engineConfig:
      cacheSizeGB: 0.5
      
 2.对数据库collection限制大小。
 db.createCollection("table", { capped : true, size : 512000000} )
 
 3.添加索引解决分页报错问题
 db.table.createIndex({"time":-1})
```

## supervisor
```
配置supervisor对读取入库进程进行监控
```

## License

See the LICENSE file.
