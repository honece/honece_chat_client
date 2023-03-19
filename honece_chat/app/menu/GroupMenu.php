<?php
namespace app\menu;

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
        (new $this)->start();
    }

    function addGroup()
    {
        $this->output->writeln("请输入群名称");
        $groupName = trim(fgets(STDIN));

        $groupInfo = Group::where('group_name', $groupName)->find();
        $groupId   = GroupMember::where(['member_id' => USER['id'], 'group_id' => $groupInfo->id])->value('id');
        if ($groupId) {
            $this->output->writeln('您已经在群中了');
            (new $this)->start();
        }
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
                $action = '创建';
            }
            else {
                GroupMember::create([
                    'member_id' => USER['id'],
                    'group_id'  => $groupInfo->id,
                    'type'      => 1
                ]);
                $action = '加入';
            }
            Db::commit();
            $this->output->writeln($action . '群"' . $groupName . '"成功');
            (new $this)->start();
        } catch (\Throwable $th) {
            Db::rollback();
            $this->output->writeln('未知原因，失败');
            (new $this)->start();
        }
    }

    function chatGroup()
    {
    }

    function delGroup()
    {
        $this->output->writeln("请输入群名称");
        $groupName = trim(fgets(STDIN));
        $groupId   = Group::where([
            'member_id'  => USER['id'],
            'group_name' => $groupName,
            'type'       => 0
        ])->value('id');
        if ($groupId) {
            Group::destroy($groupId);
            $this->output->writeln('删除群"' . $groupName . '"成功');
        }
        else {
            $this->output->writeln('没有找到该群,或群不属于您');
        }
        (new $this)->start();
    }

    function outGroup()
    {
        $this->output->writeln("请输入群名称");
        $groupName = trim(fgets(STDIN));
        
    }
}