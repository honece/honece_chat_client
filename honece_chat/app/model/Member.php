<?php
declare(strict_types=1);

namespace app\model;

use think\Model;

/**
 * @mixin \think\Model
 */
class Member extends Model
{
    protected $pk = 'id';
    protected $field = ['account'];
    function showMenu()
    {
        $this->output->writeln("-------------" . $this->menuName . "-------------");
        foreach ($this->subMenu as $key => $value) {
            $this->output->writeln(self::$menu_id++ . "." . $value);
            $this->menuActions[] = $key;
        }
        //如果不是主菜单添加主菜单选项
        if ('MainMenu' != (new \ReflectionClass($this))->getShortName()) {
            $this->menuActions[] = 'mainMenu';
            $this->output->writeln(self::$menu_id . ".主菜单");
        }
        $this->output->writeln("0.退出");
        $this->output->writeln("-------------------------------------------------");
        self::$menu_id = 1;
    }
    public function listFriend()
    {
        return $this->hasOne(Friend::class, "friend_id", "id");
    }
}