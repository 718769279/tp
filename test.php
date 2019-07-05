<?php
/**
 * Created by PhpStorm.
 * User: wangwen
 * Date: 19-7-5
 * Time: 上午11:36
 */
try {

    if ($pdo = new PDO("dm:host=localhost:5236/PRODUCTION", "SYSDBA", "123456789"))
    {
        echo "connect success！";
    }else{
        echo "connect fail！";
    }

} catch (PDOException $e) {

    print "Error: " . $e->getMessage() . "<br/>";

    die();
}