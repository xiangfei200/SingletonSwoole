<?php
/**
 * @Author: dxf
 * @Date: 2020-03-17
 * @Time: 10:39
 */

/**
 * http服务
 */
class HttpServer {

    private static $instance;
    private static $server;

    //不让类在外面创建
    private function __construct()
    {
        //创建websocket对象
        self::$server = new \Swoole\Http\Server("0.0.0.0", 9501);
        //注册事件
        //服务器启动时返回响应
        self::$server->on('Start', [$this,"onStart"]);

        // 向服务器发送请求时返回响应
        // 可以获取请求参数，也可以设置响应头和响应内容
        self::$server->on('Request', [$this,"onRequest"]);

        self::$server->on('Receive', [$this,"onReceive"]);
    }

    //覆盖公共方法，防止外部克隆
    private function __clone()
    {
        // TODO: Implement __clone() method.
    }


    /**
     * 对外的创建函数
     * @return mixed
     */
    public static function getInstance()
    {
        if(!self::$instance instanceof self){
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function OnStart($server){
        echo "Swoole http server is started at http://192.168.10.10:9501\n";
    }


    public function OnRequest($request, $response){
        $response->header("Content-Type", "text/plain");
        $response->end("Hello World\n");
    }

    public function onReceive($server, $fd, $from_id, $data){
        $server->send($fd, 'Swoole: ' . $data);
        $server->close($fd);
    }

    public function start(){
        self::$server->start();
    }

}
$http_server = HttpServer::getInstance();
$http_server->start();