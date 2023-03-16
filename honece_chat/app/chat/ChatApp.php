<?php
namespace app\chat;

use app\facade\MainMenu;
use app\model\Member;

class ChatApp extends TcpClient
{

    function main()
    {
        $this->output->writeln('请输入登录用户');
        $member = trim(fgets(STDIN));

        $user = Member::where('account', $member)->find();
        if (!$user) {
            $user = Member::create(['account' => $member]);
        }
        define('USER', ['name' => $user->account, 'id' => $user->id]);

        Action::send('login');
        MainMenu::start();
        while ($msg = fgets(STDIN)) {
            self::$conn->send($msg);
        }
    }


}