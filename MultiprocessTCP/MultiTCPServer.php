<?php


/**
 * @Author: dxf
 * @Date: 2020-03-17
 * @Time: 17:17
 */

/**
 * 多进程TCP服务器
 */
class MultiTCPServer{
    //系统支持的最大子进程数
    const MAX_PROCESS = 3;
    //子进程pid数组
    private $pids = [];
    //网络套接字
    private $socket;
    //主进程ID
    private $mpid;

    /**
     * 服务器主进程业务逻辑
     */
    public function run(){
        $process = new \Swoole\Process(function(){
            $this->mpid = getmypid();
            echo time()."Master process,pid {$this->mpid}";

            //创建tcp服务器并获取套接字
            $this->socket = stream_socket_server("tcp://192.168.10.10:9503", $errno, $errstr);

            if (!$this->socket) {
                exit("Server start error: $errstr --- $errno");
            }

            //启动子进程处理请求

            for ($i=0;$i<self::MAX_PROCESS;$i++){
                $this->startWorkerProcess();
            }

            echo "Waiting client start...\n";

            // 主进程等待子进程退出，必须是死循环
            while (1) {
                foreach ($this->pids as $k => $pid) {
                    if ($pid) {
                        // 回收结束运行的子进程，以避免僵尸进程出现
                        $ret = \Swoole\Process::wait(false);
                        if ($ret) {
                            echo time() . " Worker process $pid exit, will start new... \n";
                            // 子进程退出后重新启动一个新的子进程
                            $this->startWorkerProcess();
                            unset($this->pids[$k]);
                        }
                    }
                }
                sleep(1); //让出 1s 时间给CPU
            }
        },false, false);
    }


    /**
     * 创建worker子进程，接收客户端连接并处理
     */
    private function startWorkerProcess()
    {
        // 子进程
        $process = new \Swoole\Process(function (\Swoole\Process $worker) {
            // 子进程业务逻辑
            $this->acceptClient($worker);
        }, false, false);
        // 启动子进程并获取子进程 ID
        $pid = $process->start();
        $this->pids[] = $pid;
    }


    /**
     * 等待客户端连接并处理
     * @param Process $worker
     */
    private function acceptClient(&$worker){

    }
}