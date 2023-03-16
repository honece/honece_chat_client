<?php
namespace app\chat;

use app\chat\TcpClient;

class Action
{
    static function send($action, $data = null)
    {
        ChatApp::$conn->send(json_encode([
            'user'   => USER,
            'action' => $action,
            'data'   => $data
        ]));

    }
    static function recive($action, $data)
    {

    }
}