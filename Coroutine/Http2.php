<?php
/**
 * @Author: dxf
 * @Date: 2020-04-01
 * @Time: 16:50
 */

$server = new \Swoole\Http\Server('192.168.10.10', 9588);

$server->on('Request', function ($request, $response) {

    $channel = new \Swoole\Coroutine\Channel(3);

    go(function () use ($channel) {
        var_dump(time());

        $mysql = new \Swoole\Coroutine\MySQL();
        $mysql->connect([
                            'host'     => '192.168.10.10',
                            'user'     => 'homestead',
                            'password' => 'secret',
                            'database' => 'test',
                        ]);

        $result = $mysql->query('select sleep(3)');
        $channel->push($result);
    });

    go(function () use ($channel) {
        var_dump(time());

        $redis2 = new \Swoole\Coroutine\Redis();
        $redis2->connect('192.168.10.10', 6379);

        $result = $redis2->set('hello','world');
        $channel->push($result);
    });

    go(function () use ($channel) {
        var_dump(time());

        $redis2 = new Swoole\Coroutine\Redis();
        $redis2->connect('127.0.0.1', 6379);

        $result = $redis2->get('hello');
        $channel->push($result);
    });

    $result = [];
    for ($i = 0; $i < 3; $i++) {
        $result[] = $channel->pop();
    }
    $response->end(json_encode([
                                   'data' => $result,
                                   'time' => time(),
                               ]));
});

$server->start();