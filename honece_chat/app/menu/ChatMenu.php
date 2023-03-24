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
    protected static string $type;
    function setMenu($chatObj, $type)
    {
        $this->subMenu = $chatObj;
        self::$type    = $type;
        $this->start();
    }
    function getAction($menuId)
    {
        @$menufunc = $this->menuActions[$menuId];
        if (!$menufunc) {
            $this->output->writeln("输入错误，请重新输入");
            $this->setMenu($this->subMenu, self::$type);
        }

        if (!method_exists($this, $menufunc)) {
            $this->sendMsg($menufunc);
        }
        else {
            $this->$menufunc();
        }

    }

    function sendMsg($chatId)
    {
        if (self::$type == 'friend') {
            $this->output->writeln("您正在和{$this->subMenu[$chatId]}聊天，请发送消息");
        }
        else if (self::$type == 'group') {
            $this->output->writeln("您正在[{$this->subMenu[$chatId]}]群组聊天，请发送消息");
        }

        $this->output->writeln("键入 [exit] 返回菜单");
        try {
            while (true) {
                $msg = trim(fgets(STDIN));
                if ($msg == 'exit') {
                    return $this->setMenu($this->subMenu, self::$type);
                }
                if (self::$type == 'friend') {
                    Action::send('chat', ['friend_id' => $chatId, 'msg' => $msg]);
                }
                else if (self::$type == 'group') {
                    Action::send('chatGroup', ['group_id' => $chatId, 'msg' => $msg]);
                }
            }
        } catch (\Throwable $th) {
            var_dump($th->getFile(), $th->getLine());
        }

    }

}