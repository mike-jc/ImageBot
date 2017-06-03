<?php

namespace App\Command\Image;

use App\Command\BaseCommand;
use App\Service\Queue\ManagerInterface as QueueManagerInterface;
use App\Service\Resize\ResizerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ResizeCommand extends BaseCommand  {

    protected function configure() {
        $this
            ->setName('resize')
            ->setDefinition([
                new InputOption('number', 'n', InputOption::VALUE_REQUIRED, 'Number of files to resize'),
            ])
            ->setDescription('Resize scheduled files')
            ->setHelp(<<<'EOF'
The <info>%command.name%</info> resize scheduled image files to upload them by <info>upload</info>:

  <info>%command.full_name% [-n <count>]</info>
EOF
            )
        ;
    }

    public function safeExecute(InputInterface $input, OutputInterface $output) {
        $number = $input->getOption('number');

        /** @var QueueManagerInterface $qManager */
        $qManager = $this->container['queue-manager'];
        /** @var ResizerInterface $resizer */
        $resizer = $this->container['resizer'];

        $successfulFiles = [];
        $failedFiles = [];
        $files = $qManager->getFromQueue($qManager->getResizeQueue(), $number);

        foreach ($files as $file) {
            try {
                $successfulFiles[] = $resizer->resize($file);
            } catch (\Exception $e) {
                $failedFiles[] = $file;
            }
        }

        $qManager->putToQueue($qManager->getUploadQueue(), $successfulFiles);
        $qManager->putToQueue($qManager->getFailedQueue(), $failedFiles);

        $output->writeln(count($successfulFiles) .' file(s) resized, '. count($failedFiles) .' files added to failed queue');
    }
}