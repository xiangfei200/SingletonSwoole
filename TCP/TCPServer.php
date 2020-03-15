<?php


class TCPServer{

    private static $instance;
    private static $server;
    private $messageHandler;//处理消息

    //不让类在外面创建
    private function __construct()
    {
        //创建websocket对象
        self::$server = new \Swoole\Server("0.0.0.0", 9501);
        //注册事件
        //监听tcp连接进入事件
        self::$server->on('Connect', [$this,"onConnect"]);

        //监听tcp数据接收事件
        self::$server->on('Receive', [$this,"onReceive"]);

        //监听tcp连接关闭事件
        //不管是服务器还是客户端，该函数都会执行
        self::$server->on('Close', [$this,"onClose"]);

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

    public function onConnect($server, $fd){
        echo $fd."用户连接了\n";
//        var_dump($request->fd, $request->get, $request->server);
    }

    public function onReceive($server, $fd,$from_id,$data){
//        self::$server->reload();  //热部署
        echo "Message:接收到客户端ID：{$fd}发来的消息：{$data}\n";
        $server->send($fd,"Server:接收到了消息:{$data}\n");
    }

    public function onClose($server, $fd){
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

TCPServer::getInstance()->start();