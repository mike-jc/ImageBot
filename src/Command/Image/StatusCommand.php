<?php

namespace App\Command\Image;

use App\Command\BaseCommand;
use App\Service\Queue\ManagerInterface as QueueManagerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StatusCommand extends BaseCommand {

    protected function configure() {
        $this
            ->setName('status')
            ->setDescription('Show status of all queues')
            ->setHelp(<<<'EOF'
The <info>%command.name%</info> show all queues with a count of files in each of them
EOF
            )
        ;
    }

    public function safeExecute(InputInterface $input, OutputInterface $output) {
        /** @var QueueManagerInterface $qManager */
        $qManager = $this->container['queue-manager'];

        $output->writeln("Queue\t Count");

        foreach ($qManager->getAllQueues() as $qName => $queue) {
            $len = !is_null($len = $qManager->getQueueLength($queue)) ? $len : '?';
            $output->writeln("$qName\t $len");
        }
    }
}