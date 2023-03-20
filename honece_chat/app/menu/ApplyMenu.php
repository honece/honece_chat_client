<?php
namespace app\menu;

use app\chat\Action;
use app\menu\base\Menu;
use app\model\GroupMember;
use app\model\Member;
use app\model\Msgbox;
use app\model\Friend;
use app\model\Group;
use think\facade\Db;

class ApplyMenu extends Menu
{
    protected string $menuName = '申请信息';

    protected array $subMenu;

    private array $friendMemberInfo;

    private array $groupMemberInfo;

    private array $groupInfo;

    private array $msg;

    function start()
    {
        $this->getSubMenu();
        parent::start();
    }
    function getSubMenu()
    {
        try {
            Friend::getMemberInfo();
            $this->msg = Msgbox::where('recv', USER['id'])->where('status', 0)->Column('send,recv,type,group_id', 'id');
            if (empty($this->msg)) {
                $this->output->writeln('没有申请信息');
                (new MainMenu)->start();
            }
            /**
             * 设置菜单&&区分好友和群信息
             */
            foreach ($this->msg as $key => $value) {
                if ($value['type'] == 0) {
                    $friendIds[$value['id']] = [
                        'send' => $value['send']
                    ];
                }
                if ($value['type'] == 2) {
                    $grouplist[$value['id']] = [
                        'send'     => $value['send'],
                        'group_id' => $value['group_id']
                    ];
                }
            }
            if (!empty($friendIds)) {
                $this->friendMemberInfo = Member::whereIn('id', array_column($friendIds, 'send'))->column('account', 'id');
                foreach ($friendIds as $key => $value) {
                    $this->subMenu['msg_' . $key] = $this->friendMemberInfo[$value['send']] . "申请加您为好友";
                }
            }

            if (!empty($grouplist)) {
                //TODO
                $this->groupInfo       = Group::whereIn('id', array_column($grouplist, 'group_id'))->column('group_name', 'id');
                $this->groupMemberInfo = Member::whereIn('id', array_column($grouplist, 'send'))->column('account', 'id');
                foreach ($grouplist as $key => $value) {
                    $this->subMenu['msg_' . $key] =
                        $this->groupMemberInfo[$value['send']] .
                        "申请加入群:" .
                        $this->groupInfo[$value['group_id']];
                }
            }

        } catch (\Throwable $th) {
            echo $th->getMessage();
        }
    }

    function getAction($menuId)
    {
        @$menufunc = $this->menuActions[$menuId];
        if (!$menufunc) {
            $this->output->writeln("输入错误，请重新输入");
            (new $this)->start($menuId);
        }
        if (!method_exists($this, $menufunc)) {
            $this->showMsg($menufunc);
        }
        $this->$menufunc();
    }

    function showMsg($menufunc)
    {
        //消息id
        $msg_id = substr($menufunc, 4);

        $this->output->writeln("是否同意申请[Y/n]");
        while (true) {
            $msg = trim(fgets(STDIN));
            if ($msg == 'Y') {
                if ($this->msg[$msg_id]['type'] == 0) {
                    $this->addfriend($msg_id);
                }
                else if ($this->msg[$msg_id]['type'] == 2) {
                    $this->addGroup($msg_id);
                }
            }
            else if ($msg == 'n') {
                Msgbox::where('id', $msg_id)->update(['status' => '2']);
                $this->output->writeln("您已拒绝");
                (new $this)->start();
                break;
            }
            else {
                $this->output->writeln("输入错误，请重新输入");
            }
        }

        //返回消息列表
        (new $this)->start();

    }
    function addfriend($msg_id)
    {
        $send = $this->msg[$msg_id]['send']; //发送人
        $recv = $this->msg[$msg_id]['recv']; //接收人

        Db::startTrans();
        try {
            Friend::create([
                'member_id' => $recv,
                'friend_id' => $send
            ]);
            Friend::create([
                'member_id' => $send,
                'friend_id' => $recv
            ]);
            Msgbox::update(['status' => '1'], ['id' => $msg_id]);
            Db::commit();
            $this->output->writeln("添加好友成功");
            (new $this)->start();

        } catch (\Throwable $th) {
            Db::rollback();
            $this->output->writeln("添加好友失败");
            (new $this)->start();
        }

    }
    function addGroup($msg_id)
    {
        $memberId = $this->msg[$msg_id]['send']; //发送人
        $groupId  = $this->msg[$msg_id]['group_id'];

        Db::startTrans();
        try {
            GroupMember::create([
                'group_id'  => $groupId,
                'member_id' => $memberId
            ]);
            Msgbox::update(['status' => '1'], ['id' => $msg_id]);
            Db::commit();
            $this->output->writeln("添加成员成功");
            Action::send('joinGroup', [
                'group_id'  => $groupId,
                'member_id' => $memberId
            ]);
            (new $this)->start();

        } catch (\Throwable $th) {
            Db::rollback();
            $this->output->writeln("添加成员失败");
            (new $this)->start();
        }
    }
}