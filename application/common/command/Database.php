<?php

namespace app\common\command;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\console\input\Argument;
use think\console\input\Option;

class Database extends Command
{
    protected function configure()
    {
        $this->setName('database')
            ->addArgument('option', Argument::OPTIONAL, "database options")
            ->addOption('f', null, Option::VALUE_REQUIRED, 'file url')
            ->setDescription('database options');
    }

    protected function execute(Input $input, Output $output)
    {
        $name = trim($input->getArgument('option'));

        $file = $input->getOption('f');

        switch ($name) {
            case 'import':
                $output->writeln('Start of import');
                $startTime = microtime(true);
                $dbManage = new \org\DbManage();
                $dbManage->import($file);
                $output->writeln('----It takes ' . round(microtime(true) - $startTime, 3) . ' seconds');
                break;
            case 'backup':
                $output->writeln('Backup start');
                $startTime = microtime(true);
                $dbManage = new \org\DbManage();
                $dbManage->back($file, date('Y-m-d H:i:s') . '自动备份');
                $output->writeln('----It takes ' . round(microtime(true) - $startTime, 3) . ' seconds');
                break;
            default:
                $output->writeln('This operation is not supported');
                break;
        }

        // $name = $name ?: 'thinkphp';
        // dump($name);
        // die;
        // $output->writeln('备份开始');
        // $startTime = microtime(true);
        // $dbManage = new \org\DbManage();
        // $dbManage->back();
        // $output->writeln('----耗时: ' . round(microtime(true) - $startTime, 3) . '秒');
        // $output->writeln('success');
        // $output->writeln("\n");
    }
}
