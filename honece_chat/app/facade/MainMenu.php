<?php
namespace app\facade;

use think\Facade;
class MainMenu extends Facade
{
    protected static function getFacadeClass()
    {
        return 'app\menu\mainMenu';
    }
}
