<?php
/**
 * @Author: dxf
 * @Date: 2020-04-01
 * @Time: 11:25
 */

$server = new \Swoole\Http\Server('192.168.10.10', 9588);

$server->on('Request', function ($request, $response) {
    var_dump(time());
    $mysql = new \Swoole\Coroutine\MySQL();
    $mysql->connect([
                        'host'     => '192.168.10.10',
                        'user'     => 'homestead',
                        'password' => 'secret',
                        'database' => 'test',
                    ]);
    $mysql->setDefer();
    $mysql->query('select sleep(3)');

    var_dump(time());

    $redis1 = new \Swoole\Coroutine\Redis();
    $redis1->connect('192.168.10.10', 6379);
    $redis1->setDefer();
    $redis1->set('hello', 'world');

    var_dump(time());

    $redis2 = new \Swoole\Coroutine\Redis();
    $redis2->connect('192.168.10.10', 6379);
    $redis2->setDefer();
    $redis2->get('hello');

    $result1 = $mysql->recv();
    $result2 = $redis2->recv();

    var_dump($result1, $result2, time());

    $response->end('Request Finish:' . time());

});

$server->start();