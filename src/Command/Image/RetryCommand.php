<?php

namespace App\Command\Image;

use App\Command\BaseCommand;
use App\Service\Queue\ManagerInterface as QueueManagerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RetryCommand extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('retry')
            ->setDefinition([
                new InputOption('number', 'n', InputOption::VALUE_REQUIRED, 'Number of files to retry with'),
            ])
            ->setDescription('Reschedule files from failed queue to resize queue')
            ->setHelp(<<<'EOF'
The <info>%command.name%</info> add failed image files to the schedule queue again to resize
by <info>resize</info>:

  <info>%command.full_name% [-n <count>]</info>
EOF
            )
        ;
    }

    public function safeExecute(InputInterface $input, OutputInterface $output)
    {
        $number = $input->getOption('number');

        /** @var QueueManagerInterface $qManager */
        $qManager = $this->container['queue-manager'];
        $number = $qManager->putToQueue($qManager->getResizeQueue(), $qManager->getFromQueue($qManager->getFailedQueue(), $number));

        $output->writeln("$number file(s) rescheduled");
    }
}