<?php
namespace app\menu;

use app\menu\base\Menu;

class GroupMenu extends Menu
{
    protected array $subMenu = [
        'showFriend' => '查看群组',
        'chatGroup'  => '进入群组',
        'delGroup'   => '退出群组'
    ];
    protected string $menuName = '主菜单';
    function showFriend()
    {
    }
    function chatGroup()
    {
    }
    function delGroup()
    {
    }
}