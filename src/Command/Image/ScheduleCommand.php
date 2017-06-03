<?php

namespace App\Command\Image;

use App\Command\BaseCommand;
use App\Service\Queue\ManagerInterface as QueueManagerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

class ScheduleCommand extends BaseCommand {

    protected function configure() {
        $this
            ->setName('schedule')
            ->setDefinition([
                new InputArgument('dir', InputArgument::REQUIRED, 'Path to directory with image files'),
            ])
            ->setDescription('Add filenames to resize queue')
            ->setHelp(<<<'EOF'
The <info>%command.name%</info> add image file to the queue to resize by <info>resize</info>:

  <info>%command.full_name% ./images</info>
EOF
            )
        ;
    }

    public function safeExecute(InputInterface $input, OutputInterface $output) {
        $dir = $input->getArgument('dir');
        $output->writeln("Scan $dir...");

        $files = [];
        $finder = new Finder();
        $finder->ignoreUnreadableDirs()->in($dir)->files()->name('/\.(png|jpg|jpeg|gif)$/');

        foreach ($finder as $file) {
            $files[] = $file->getRealPath();
        }

        /** @var QueueManagerInterface $qManager */
        $qManager = $this->container['queue-manager'];
        $qManager->putToQueue($qManager->getResizeQueue(), $files);

        $output->writeln('Scheduled '. count($files) .' file(s)');
    }
}