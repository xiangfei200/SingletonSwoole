<?php

class Database
{

    public $config = [
        'host'     => '192.168.10.10',
        'user'     => 'homestead',
        'password' => 'secret',
        'db_name'  => 'test',
        'charset'  => 'utf-8',
    ];

    public $link;

    public function __construct()
    {
        //创建链接
        $this->link = mysqli_connect($this->config['host'], $this->config['user'], $this->config['password']);
        //判断链接是否成功
        if (!$this->link) {
            exit('mysql数据链接失败');
        }
        //选择要链接的数据库
        mysqli_select_db($this->link,$this->config['db_name']);
        //设置字符集
        mysqli_set_charset($this->link, 'utf-8');

    }


    public function exec($sql)
    {
        $result = true;
        //执行查询
        try{
            $res = mysqli_query($this->link, $sql);
//        $res = mysqli_query($this->link,"select * from im");
            //处理结果集
            //释放资源
            if($res && $res !== true){
                $result = mysqli_fetch_all($res,MYSQLI_ASSOC);
                mysqli_free_result($res);
            }
            //关闭数据库
            mysqli_close($this->link);
            return $result;
        }catch (\Exception $e){
            echo $e->getMessage();
        }
    }
}