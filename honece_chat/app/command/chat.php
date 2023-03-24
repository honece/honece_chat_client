<?php
declare(strict_types=1);

namespace app\command;

use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;

class chat extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('Chatapp run')
            ->setDescription('the Chat app start...');
    }

    protected function execute(Input $input, Output $output)
    {
        $cahtApp = invoke('app\chat\chatApp');
    }
}