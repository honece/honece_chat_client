<?php
namespace app\menu;

use app\chat\Action;
use app\chat\ChatApp;
use app\menu\base\Menu;
use app\model\Friend;
use app\model\Member;

class FriendMenu extends Menu
{
    protected array $subMenu = [
        'chatFriend' => '和在线好友聊天',
        'addFriend'  => '添加好友',
        'delFriend'  => '删除好友'
    ];
    protected string $menuName = '好友菜单';
    function chatFriend()
    {
        //和在线好友聊天，显示在线好友菜单
        $friendInfo = Member::
            hasWhere(
                'listFriend',
                ['member_id' => USER['id']]
            )
            ->where('status', 0)
            ->select()->toArray();
        foreach ($friendInfo as $key => $value) {
            $friend[$value['id']] = $value['account'];
        }
        (new ChatMenu)->showFriend($friend);
    }
    function addFriend()
    {
        $this->output->writeln("请输入添加账号");
        $friend   = trim(fgets(STDIN));
        $friendId = Member::where('account', $friend)
            ->where('account', '<>', USER['name'])
            ->value('id');
        if (!$friendId) {
            $this->output->writeln("没有这个用户,请重新输入");
            (new $this)->start();
        }

        Action::send(
            'addfriend',
            ['friend_id' => $friendId]
        );
        $this->output->writeln("已经发送添加好友请求");
        (new $this)->start();
    }
    function delFriend()
    {
        //删除好友
    }
    // TODO 好友申请菜单
    function showMsg()
    {

    }
}