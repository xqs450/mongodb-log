<?php

spl_autoload_register(function ($className) {
    $pos =  strpos($className, 'MongodbLog');
    if ($pos !== 0) {
        return;
    }
    $fileName = substr($className,strlen("MongodbLog"));
    $filePathName = dirname(__DIR__);
    $absFile = $filePathName."/".$fileName.".php";

    if (is_file($absFile)) {
        require_once $absFile;
    }
});

function requireDependencies() {
    $requiredExtensions = ['mongodb'];
    foreach ($requiredExtensions AS $ext) {
        if (!extension_loaded($ext)) {
            throw new Exception('The MongodbLog library requires the ' . $ext . ' extension.');
        }
    }
}

if (version_compare(PHP_VERSION, '5.4.0', '<')) {
    throw new Exception('PHP version >= 5.4.0 required');
}

requireDependencies();
//$autoPath = __DIR__ . DIRECTORY_SEPARATOR.'/../vendor/autoload.php';
//require_once (__DIR__ . DIRECTORY_SEPARATOR.'/../vendor/autoload.php');
//require_once(__DIR__ . DIRECTORY_SEPARATOR . '/../src/SearchLogApp.php');
//require_once(__DIR__ . DIRECTORY_SEPARATOR . '/../src/WriteLogApp.php');