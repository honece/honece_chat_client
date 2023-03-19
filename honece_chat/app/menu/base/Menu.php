<?php
namespace app\menu\base;

use app\chat\TcpClient;
use app\menu\MainMenu;
use think\console\Output;
use Swoole\Server;

class Menu
{
    protected array $subMenu;
    protected string $menuName;
    protected $output;
    private static $menu_id = 1;
    protected $menuActions = ['over'];

    function __construct()
    {
        $this->output = new Output;
    }

    function start()
    {
        $this->showMenu();
        $this->getAction(trim(fgets(STDIN)));
    }

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

    function getAction($menuId)
    {
        @$menuFunc = $this->menuActions[$menuId];
        if (!$menuFunc) {
            $this->output->writeln("输入错误，请重新输入");
            (new $this)->start();
        }
        $this->$menuFunc();
    }
    function over()
    {
        TcpClient::$conn->close();
        $this->output->writeln("请键入Ctrl+C以结束程序");
    }
    function mainMenu()
    {
        (new MainMenu)->start();
    }

}