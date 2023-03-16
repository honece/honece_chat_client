<?php
namespace app\menu;

use app\menu\base\Menu;

class MainMenu extends Menu
{
    protected array $subMenu = [
        'friend' => '好友',
        'group'  => '群组',
    ];
    protected string $menuName = '主菜单';
    function friend()
    {
        (new FriendMenu)->start();
    }
    function group()
    {
        (new GroupMenu)->start();
    }
}