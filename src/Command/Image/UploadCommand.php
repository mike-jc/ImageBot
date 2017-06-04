<?php

namespace App\Command\Image;

use App\Command\BaseCommand;
use App\Service\Queue\ManagerInterface as QueueManagerInterface;
use App\Service\Upload\ManagerInterface as UploadManagerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UploadCommand extends BaseCommand {

    protected function configure() {
        $this
            ->setName('upload')
            ->setDefinition([
                new InputOption('number', 'n', InputOption::VALUE_REQUIRED, 'Number of image files to upload'),
            ])
            ->setDescription('Upload files to the storage')
            ->setHelp(<<<'EOF'
The <info>%command.name%</info> upload resized image files to the storage

  <info>%command.full_name% [-n <count>]</info>
EOF
            )
        ;
    }

    public function safeExecute(InputInterface $input, OutputInterface $output) {
        $number = $input->getOption('number');

        /** @var QueueManagerInterface $qManager */
        $qManager = $this->container['queue-manager'];
        /** @var UploadManagerInterface $uploadManager */
        $uploadManager = $this->container['upload-manager'];

        $successfulFiles = [];
        $failedFiles = [];
        $files = $qManager->getFromQueue($qManager->getUploadQueue(), $number);

        foreach ($files as $file) {
            try {
                $successfulFiles[] = $uploadManager->upload($file);
            } catch (\Exception $e) {
                $output->writeln($e->getMessage());
                $failedFiles[] = $file;
            }
        }

        $qManager->putToQueue($qManager->getDoneQueue(), $successfulFiles);
        $qManager->putToQueue($qManager->getFailedQueue(), $failedFiles);

        $output->writeln(count($successfulFiles) .' file(s) uploaded, '. count($failedFiles) .' files added to failed queue');
    }
}