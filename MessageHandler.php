<?php


/**
 * 处理消息
 * Class MessageHandler
 */
class MessageHandler{

    public $database;

    public function __construct()
    {
        require 'Database.php';
        $this->database = new Database();
    }

    public function login($client_id,$data){
        echo $client_id."用户正在登陆";
        print_r($data);
    }

    public function logout($client_id){
        echo $client_id."用户退出了";
    }

    public function content($client_id,$data){
        $content = $data['data'];
        $sql = "insert into im (client_id,data) VALUES (".$client_id.",'".$content."')";
        var_dump($sql);
        $result = $this->database->exec($sql);
    }

    public function getHistoryList(){
        $sql = "select * from im";
        $result = $this->database->exec($sql);
        print_r($result);
    }

}