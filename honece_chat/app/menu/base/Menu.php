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
        if (count($this->menuActions) == 1) {
            foreach ($this->subMenu as $key => $value) {
                $this->menuActions[] = $key;
            }
        }

        foreach ($this->subMenu as $key => $value) {
            $this->output->writeln(self::$menu_id++ . "." . $value);
        }
        //如果不是主菜单添加主菜单选项
        if ('MainMenu' != (new \ReflectionClass($this))->getShortName()) {
            if (!in_array('mainMenu', $this->menuActions)) {
                $this->menuActions[] = 'mainMenu';
            }
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
            $this->start();
        }
        $this->$menuFunc();
    }
    function over()
    {
        TcpClient::$conn->close();
        die;
    }
    function mainMenu()
    {
        (new MainMenu)->start();
    }

}