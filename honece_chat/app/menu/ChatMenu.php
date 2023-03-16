<?php
namespace app\menu;

use app\menu\base\Menu;
use app\model\Member;
use app\chat\Action;
use app\menu\MainMenu;

class ChatMenu extends Menu
{
    protected string $menuName = '在线好友';
    protected array $subMenu;
    function showFriend($friend)
    {
        $this->subMenu = $friend;
        $this->start();
    }
    function getAction($menuId)
    {
        @$menufunc = $this->menuActions[$menuId];
        if (!$menufunc) {
            $this->output->writeln("输入错误，请重新输入");
            (new $this)->showFriend($this->subMenu);
        }
        if (!method_exists($this, $menufunc)) {
            $this->sendMsg($menufunc);
        }
        parent->$menufunc();
    }

    function sendMsg($friendId)
    {
        $this->output->writeln("您正在和{$this->subMenu[$friendId]}聊天，请发送消息");
        $this->output->writeln("键入 [exit] 返回菜单");
        while ($msg = trim(fgets(STDIN))) {
            if ($msg == 'exit') {
                (new $this)->showFriend($this->subMenu);
            }
            Action::send('chat', ['friend_id' => $friendId, 'msg' => $msg]);
        }
    }

}