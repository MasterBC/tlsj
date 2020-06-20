<?php

namespace app\common\command;

use app\common\model\auth\AuthGroup;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\console\input\Argument;
use think\console\input\Option;

class Develop extends Command
{
    protected function configure()
    {
        $this->setName('develop')
            ->addArgument('option', Argument::OPTIONAL, "develop options")
            ->setDescription('develop options');
    }

    protected function execute(Input $input, Output $output)
    {
        $name = trim($input->getArgument('option'));

        switch ($name) {
            case 'generateSuperAdminAuth':
                $output->writeln('Start');
                $startTime = microtime(true);
                AuthGroup::generateSuperAdminAuth();
                $output->writeln('----It takes ' . round(microtime(true) - $startTime, 3) . ' seconds');
                break;
            default:
                $output->writeln('This operation is not supported');
                break;
        }
    }
}
