<?php
/**
 * @Author: dxf
 * @Date: 2020-03-17
 * @Time: 11:24
 */

/**
 *定时器
 */

//const N = 100000;
//
//$timeId = \Swoole\Timer::after(3000, function () {
//    echo "Laravel 也很棒\n";
//});
//
//$timeId = \Swoole\Timer::tick(2000, function ($timeId) use ($count){
//    global $count;
//    echo "Swoole 很棒\n";
//    $count++;
//    if($count == 3){
//        \Swoole\Timer::clear($timeId);
//    }
//},$count);

//\Swoole\Timer::clear($timeId);

const N = 100000;

function test()
{
    global $timers;
    shuffle($timers);
    $stime = microtime(true);
    foreach($timers as $id)
    {
        \Swoole\Timer::clear($id);
    }
    $etime = microtime(true);
    echo "del ".N." timer :". ($etime - $stime)."s\n";
}

class TestClass
{
    static function timer()
    {

    }
}

$timers = [];
$stime = microtime(true);
for($i = 0; $i < N; $i++)
{
    $timers[] = \Swoole\Timer::after(rand(1, 9999999), 'test');
    //swoole_timer_after(rand(1, 9999999), function () {
    //    echo "hello world\n";
    //});
    //swoole_timer_after(rand(1, 9999999), array('TestClass', 'timer'));
}
$etime = microtime(true);
echo "add ".N." timer :". ($etime - $stime)."s\n";
swoole_event_wait();