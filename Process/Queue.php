<?php
/**
 * @Author: dxf
 * @Date: 2020-03-20
 * @Time: 16:24
 */

/**
 * 队列
 */

$process = new \Swoole\Process(function (\Swoole\Process $worker){
    //子进程逻辑
    //从消息队列读取数据
    $cmd = $worker->pop();
//    var_dump($cmd);
    echo "Message from master process: " . $cmd . "\n";
    ob_start();
    passthru($cmd);
    $ret = ob_get_clean() ? : "";
    $ret = trim($ret).". worker pid:".$worker->pid."\n";

    //将返回消息推送到消息队列
    $worker->push($ret);
    $worker->exit(0);
});

/**
 * 调用useQueue 表示使用消息队列进行管道通信
 * 消息队列于管理通信不能共存
 * 第一个参数表示消息队列的key，第二个参数表示通信模式，2表示争抢模式
 * 使用争抢模式进行通信时，哪个子进程先读取到消息先消费，因此无法实现与指定子进程的通信
    消息队列不支持事件循环，因此引入了 \Swoole\Process::IPC_NOWAIT 表示以非阻塞模式进行通信
 */

$process->useQueue(1,2|\Swoole\Process::IPC_NOWAIT);

// 从主进程将命令推送到消息队列
$process->push('php --version');

//从消息队列中读取返回消息
$msg = $process->pop();

echo 'Message from worker process :'.$msg;

//启动子进程
$process->start();

//回收结束运行的子进程。
\Swoole\Process::wait(); /// 要调用这段代码，否则子进程中的 push 或 pop 可能会报错
