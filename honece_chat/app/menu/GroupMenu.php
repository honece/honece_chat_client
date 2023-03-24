<?php
namespace app\menu;

use app\chat\Action;
use app\menu\base\Menu;
use app\model\Group;
use app\model\GroupMember;
use think\facade\Db;

class GroupMenu extends Menu
{

    protected array $subMenu = [
        'showGroup' => '查看群组',
        'addGroup'  => '添加群组',
        'chatGroup' => '进入群组',
        'delGroup'  => '删除群组',
        'outGroup'  => '退出群组'
    ];
    protected string $menuName = '群组菜单';
    function showGroup()
    {
        $groupName = Group::hasWhere('getGroup', ['member_id' => USER['id']])->column('Group.group_name');
        if (empty($groupName)) {
            $this->output->writeln("未找到群组信息");

        }
        else {
            $this->output->writeln("*************************");
            foreach ($groupName as $key => $value) {
                $this->output->writeln(($key + 1) . '.' . $value);
            }
            $this->output->writeln("*************************");
        }
        $this->start();
    }

    function addGroup()
    {
        $this->output->writeln("请输入群名称");
        $groupName = trim(fgets(STDIN));
        $groupInfo = Group::where('group_name', $groupName)->find();


        Db::startTrans();
        try {
            if (empty($groupInfo)) {
                $group = Group::create([
                    'member_id'  => USER['id'],
                    'group_name' => $groupName
                ]);
                GroupMember::create([
                    'member_id' => USER['id'],
                    'group_id'  => $group->id,
                    'type'      => 0
                ]);
                Action::send('joinGroup', [
                    'group_id'  => $group->id,
                    'member_id' => USER['id']
                ]);
                $this->output->writeln('创建群"' . $groupName . '"成功');
            }
            else {
                $groupId = GroupMember::where(['member_id' => USER['id'], 'group_id' => $groupInfo->id])->value('id');
                if ($groupId) {
                    $this->output->writeln('您已经在群中了');
                    $this->start();
                }
                Action::send('addGroup', [
                    'recv_id'  => $groupInfo->member_id,
                    'group_id' => $groupInfo->id
                ]);
                $this->output->writeln('已经向"' . $groupName . '"发送申请');
            }
            Db::commit();
            $this->start();
        } catch (\Throwable $th) {
            Db::rollback();
            $this->output->writeln('未知原因，失败');
            $this->start();
        }
    }

    function chatGroup()
    {
        $group = Group::hasWhere('getGroup', ['member_id' => USER['id']])->column('Group.group_name', 'Group.id');
        if (!empty($group)) {
            (new ChatGroup)->setMenu($group, 'group');
        }
        else {
            $this->output->writeln("没有已加入的群组");
            $this->start();
        }
    }

    function delGroup()
    {
        $this->output->writeln("请输入群名称");
        $groupName = trim(fgets(STDIN));
        $groupId   = Group::where([
            'member_id'  => USER['id'],
            'group_name' => $groupName
        ])->value('id');
        Group::destroy($groupId);
        $this->output->writeln('删除群"' . $groupName . '"成功');
        $this->start();
    }

    function outGroup()
    {
        $this->output->writeln("请输入群名称");
        $groupName = trim(fgets(STDIN));
        $groupId   = Group::where([
            'group_name' => $groupName,
        ])->value('id');
        try {
            GroupMember::destroy(function ($query) use ($groupId) {
                $query->where([
                    ['member_id', '=', USER['id']],
                    ['type', '=', 1],
                    ['group_id', '=', $groupId]
                ]);
            });
        } catch (\Throwable $th) {
            echo $th->getMessage().PHP_EOL;
            $this->output->writeln("退出群失败");
        }

        $this->output->writeln("退出群成功");
        $this->start();
    }
}