<?php
use \Swoole\Client;

class NonBlockingClient{

    protected $protocol;

    /**
     * 多种协议通用的客户端，传入协议的常量
     * Client constructor.
     */
    public function __construct($protocol = SWOOLE_SOCK_TCP)
    {
        $this->protocol = $protocol;
    }

    /**
     * 客户端连接服务
     */
    public function connet(){
        //最新release版4.6 异步只支持\Swoole\Async\Client  已弃用\Swoole\Client的第二个参数
        $client = new Client($this->protocol);
        if(!$client->connect("192.168.10.10",9501)){
            exit("Connect failed");
        }
        $client->send("hello world!");
        echo $client->recv();
        $client->close();
    }

}
$client = new NonBlockingClient();
$client->connet();