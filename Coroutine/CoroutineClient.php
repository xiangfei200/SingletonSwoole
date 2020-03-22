<?php

// 通过 go 函数创建一个协程
//go(function (){
//    $client = new \Swoole\Coroutine\Client(SWOOLE_SOCK_TCP);
//    //尝试与指定tcp服务器建立连接，这里会触发IO事件切换协程，交出控制权让CPU去处理其他事情
//    if($client->connect("127.0.0.1",9501,0.5)){
//        //建立连接后发送内容
//        $client->send("hello world\n");
//
//        echo $client->recv();
//        $client->close();
//    }else{
//        echo "connect failed";
//    }
//});


go(function () {
    try {
        Swoole\Coroutine::sleep(1);  // 模拟 IO 事件让出控制权
        exit(SWOOLE_EXIT_IN_COROUTINE);
    } catch (Swoole\ExitException $exception) {
        assert($exception->getStatus() === 1);
        assert($exception->getFlags() === SWOOLE_EXIT_IN_COROUTINE);
        echo $exception->getMessage();
        return $exception->getMessage();
    }
});