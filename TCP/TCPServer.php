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

        //处理TCP粘包问题
        //设置异步任务的工作进程数量
        self::$server->set(array(
                         'task_worker_num' => 4,
//                         'open_length_check' => true,
//                         'package_max_length' => 81920,
//                         'package_length_type' => 'n', //see php pack()
//                         'package_length_offset' => 0,
//                         'package_body_offset' => 2,
                     ));
        //监听tcp数据接收事件
        self::$server->on('Receive', [$this,"onReceive"]);

        //监听tcp连接关闭事件
        //不管是服务器还是客户端，该函数都会执行
        self::$server->on('Close', [$this,"onClose"]);

        // 处理异步任务
        self::$server->on('Task', [$this,"onTask"]);


        // 处理异步任务的结果
        self::$server->on('Finish', [$this,"onFinish"]);

        //在worker启动时自动加载处理消息的类
        //此事件在 Worker 进程 /Task 进程启动时发生，这里创建的对象可以在进程生命周期内使用。
//        self::$server->on('workerStart', [$this,"onWorkerStart"]);
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
        //投递异步任务
        $task_id = $server->task($data);
        echo getmypid()."异步任务投递成功: id=$task_id\n";
        $server->send($fd,"Server:接收到了消息:{$data}，处理中...\n");
    }


    public function onTask($server, $task_id, $from_id, $data){
        echo "新的待处理异步任务[id=$task_id]".PHP_EOL;
        // todo 处理异步任务
        sleep(2);
        // 返回任务执行的结果
        $server->finish("$data -> OK");
    }

    public function onFinish(\Swoole\Server $server, $task_id, $data){
        echo "异步任务[$task_id] 处理完成: $data".PHP_EOL;
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

MultiTCPServer::getInstance()->start();