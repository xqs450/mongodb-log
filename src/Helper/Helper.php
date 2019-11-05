<?php
/**
 * Created by PhpStorm.
 * User: Qson
 * Date: 2019/11/4
 * Time: 14:41
 */
namespace MongodbLog\Helper;
class Helper{
    public static function getClientIpAddress(){
        if(!empty($_SERVER['HTTP_CLIENT_IP'])){
            //ip from share internet
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        }elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
            //ip pass from proxy
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }else{
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    public static function replaceEmptyKey($arr){
        if(!is_array($arr))return $arr;
        $newArray = [];
        foreach($arr as $key=>$value){
            if(is_array($value)){
                $value=self::replaceEmptyKey($value);
            }
            if($key === ""){
                $newArray["_"] = $value;
            }else{
                $newArray[$key] = $value;
            }
        }
        return $newArray;
    }
}
