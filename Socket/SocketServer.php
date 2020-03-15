<?php

//创建单例模式类
class SocketServer{
    //三私一公
    private static $instance;
    private static $server;
    private $messageHandler;//处理消息

    //不让类在外面创建
    private function __construct()
    {
        //创建websocket对象
        self::$server = new \Swoole\WebSocket\Server("0.0.0.0", 9502);
        //注册事件
        //监听WebSocket连接打开事件
        self::$server->on('open', [$this,"onOpen"]);

        //监听WebSocket消息事件
        self::$server->on('message', [$this,"onMessage"]);

        //监听WebSocket连接关闭事件
        //不管是服务器还是客户端，该函数都会执行
        self::$server->on('close', [$this,"onClose"]);

        //在worker启动时自动加载处理消息的类
        //此事件在 Worker 进程 /Task 进程启动时发生，这里创建的对象可以在进程生命周期内使用。
        self::$server->on('workerStart', [$this,"onWorkerStart"]);
        //启动在start方法里启动
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

    public function onOpen($ws, $request){
        echo $request->fd."用户连接了";
//        var_dump($request->fd, $request->get, $request->server);
    }

    public function onMessage($ws, $request){
        self::$server->reload();  //热部署

        echo "Message: {$request->data}\n";
        //约定的json格式为{"cmd":"xx","data":"xxx"}
        $data = json_decode($request->data,true);
        if(method_exists($this->messageHandler,$data['cmd'])){
            call_user_func([$this->messageHandler,$data['cmd']],$request->fd,$data);
        }
//        foreach ($ws->connections as $fd){
//            $ws->push($fd, "{$request->data}");
//        }
    }

    public function onClose($ws, $fd){
        echo "client-{$fd} is closed\n";
    }


    public function onWorkerStart(){
//        echo "OnWorkerStart .......";
        require __DIR__ . '/../MessageHandler.php';
        $this->messageHandler = new MessageHandler();
    }


    public function start(){
        self::$server->start();
    }
}


SocketServer::getInstance()->start();