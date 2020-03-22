<?php
$server = new \Swoole\Server("127.0.0.1", 9501);

// 调用 onReceive 事件回调函数时底层会自动创建一个协程
$server->on('receive', function ($serv, $fd, $from_id, $data) {
    // 向客户端发送数据后关闭连接（在这里面可以调用 Swoole 协程 API）
    $serv->send($fd, 'Swoole: ' . $data);
    $serv->close($fd);
});

$server->start();