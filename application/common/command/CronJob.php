<?php

namespace app\common\command;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\console\input\Argument;
use app\common\model\work\MoneyWebDay;
use app\common\model\Users;

class CronJob extends Command
{

    protected function configure()
    {
        $this->setName('cronJob')
                ->addArgument('option', Argument::OPTIONAL, "cron job options")
                ->setDescription('cron job');
    }

    protected function execute(Input $input, Output $output)
    {
        $name = trim($input->getArgument('option'));
        switch ($name) {
            case 'autoUserBonus': // 每天中午  12 点 执行   php think cronJob automaticMining
                $output->writeln('开始检测每日分红');
                $startTime = microtime(true);
                (new MoneyWebDay())->autoUserBonus();
                $output->writeln('----It takes ' . round(microtime(true) - $startTime, 3) . ' seconds');
                break;
            case 'autoDayGiveTurnNum': // 每天中午 0 点 执行   php think cronJob automaticMining
                $output->writeln('开始检测赠送转盘劵');
                $startTime = microtime(true);
                (new Users())->autoDayGiveTurnNum();
                $output->writeln('----It takes ' . round(microtime(true) - $startTime, 3) . ' seconds');
                break;
            case 'autoDayGiveShakeNum': // 每天中午 0 点 执行   php think cronJob automaticMining
                $output->writeln('检测摇一摇次数');
                $startTime = microtime(true);
                (new Users())->autoDayGiveShakeNum();
                $output->writeln('----It takes ' . round(microtime(true) - $startTime, 3) . ' seconds');
                break;
            case 'autoDayGiveVideoNum': // 每天中午 0 点 执行   php think cronJob automaticMining
                $output->writeln('检测视频次数');
                $startTime = microtime(true);
                (new Users())->autoDayGiveVideoNum();
                $output->writeln('----It takes ' . round(microtime(true) - $startTime, 3) . ' seconds');
                break;
            default:
                $output->writeln('This operation is not supported');
                break;
        }
    }

}
