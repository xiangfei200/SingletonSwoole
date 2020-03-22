<?php

$workNum = 5;

$pool = new \Swoole\Process\Pool($workNum);

$pool->on("WorkerStart",function($pool,$workerId){
    echo "Worker#{$workerId} is started";
    $redis = new \Redis();
    $redis->pconnect("192.168.10.10",6379);
    $key = "key1";
    while(true){
        $msg = $redis->brPop($key,2);
        if($msg == null){
            continue;
        }
        var_dump($msg);
        echo "Processed by worker #{$workerId}";
    }
});

$pool->on("WorkerStop",function($pool,$workerId){
    echo "Worker #{$workerId} is stopped \n";
});

$pool->start();
