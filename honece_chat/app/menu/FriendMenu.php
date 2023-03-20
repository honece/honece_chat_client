<?php
namespace app\menu;

use app\chat\Action;
use app\chat\ChatApp;
use app\menu\base\Menu;
use app\model\Friend;
use app\model\Member;

class FriendMenu extends Menu
{
    private $friend;

    protected array $subMenu = [
        'chatFriend' => '和在线好友聊天',
        'addFriend'  => '添加好友',
        'delFriend'  => '删除好友'
    ];
    protected string $menuName = '好友菜单';

    function __construct()
    {
        parent::__construct();
        try {
            //放在这里不好，每次new对象都会重新查数据库，增加网络IO，但是每次都会检查最新的在线好友列表
            $this->friend = Friend::getMemberInfo();
        } catch (\Throwable $th) {
            echo $th->getMessage();
        }
    }
    function chatFriend()
    {
        foreach ($this->friend as $key => $value) {
            $friend[$value['id']] = $value['account'];
        }
        if (!empty($friend)) {
            (new ChatMenu)->setMenu($friend,'friend');
        }
        else {
            $this->output->writeln("没有好友在线");
            (new $this)->start();
        }
    }
    //添加好友，应该检查一下是否存在好友的
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
        $this->output->writeln("请输入好友账号");
        $friend = trim(fgets(STDIN));
        //这里正常情况下需要请求server，为了方便，直接删除
        //删除好友
        try {
            foreach ($this->friend as $key => $value) {
                if ($value['account'] == $friend) {
                    $friendId = $value['id'];
                }
            }
            if (!isset($friendId)) {
                $this->output->writeln("未找到好友");
                (new $this)->start();
            }
            Friend::whereOr([
                [
                    ['friend_id', '=', USER['id']],
                    ['member_id', '=', $friendId]
                ],
                [
                    ['friend_id', '=', $friendId],
                    ['member_id', '=', USER['id']]
                ]
            ])->delete();

            $this->output->writeln("删除好友成功");
            (new $this)->start();
        } catch (\Throwable $th) {
            $this->output->writeln("删除好友失败");
            (new $this)->start();
        }
    }
}