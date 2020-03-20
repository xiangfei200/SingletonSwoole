<?php

// Process 构造函数第三个参数默认为 true，启用管道，
//如果第二个参数也设置为 true，则在子进程中可以通过 echo 将数据写入管道
$process = new \Swoole\Process(function(\Swoole\Process $worker){
    //子进程逻辑
    //1.同步阻塞模式
//    //通过管道从主进程读取数据
//    $cmd = $worker->read();
//
//    //此函数将打开输出缓冲。当输出缓冲激活后，脚本将不会输出内容（除http标头外），相反需要输出的内容被存储在内部缓冲区中。
//    ob_start();
//
//    //执行外部程序并显示未经处理的原始输出，虽然是直接输出结果，但是受到输出缓冲影响，先扔到了缓冲区里
//    //当所执行的 Unix 命令输出二进制数据， 并且需要直接传送到浏览器的时候， 需要用此函数来替代 exec() 或 system() 函数。
//    passthru($cmd);
//
//    //得到当前缓冲区的内容并删除当前输出缓冲区。
//    //
//    //ob_get_clean() 实质上是一起执行了 ob_get_contents() 和 ob_end_clean()。
//    $ret = ob_get_clean() ? :'';
//    $ret = trim($ret)." worker pid:".$worker->pid.PHP_EOL;
//
//    //将数据写进管道
//    $worker->write($ret);
//
//    //退出子进程
//    $worker->exit(0);
    //2. 异步通信模式
    \Swoole\Event::add($worker->pipe,function($pipe) use ($worker){
        // 通过管道从主进程读取数据
        $cmd = $worker->read();
        ob_start();
        // 执行外部程序并显示未经处理的原始输出，会直接打印输出
        passthru($cmd);
        $ret = ob_get_clean() ? : ' ';
        $ret = trim($ret) . ". worker pid:" . $worker->pid . "\n";
        // 将数据写入管道
        $worker->write($ret);
        $worker->exit(0);  // 退出子进程
    });

},true,1);

// 启动进程
$process->start();
// 从主进程将通过管道发送(shell命令)数据到子进程
$process->write('php --version');
// 从子进程读取返回数据并打印
$msg = $process->read();
echo 'result from worker: ' . $msg;
