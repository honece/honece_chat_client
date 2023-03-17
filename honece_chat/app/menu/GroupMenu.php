<?php
namespace app\menu;

use app\menu\base\Menu;

class GroupMenu extends Menu
{
    protected array $subMenu = [
        'showGroup' => '查看群组',
        'chatGroup'  => '进入群组',
        'delGroup'   => '退出群组'
    ];
    protected string $menuName = '主菜单';
    function showGroup()
    {
    }
    function chatGroup()
    {
    }
    function delGroup()
    {
    }
}