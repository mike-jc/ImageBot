<?php

namespace App\Command;

use Pimple\Container;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class BaseCommand extends SymfonyCommand implements CommandInterface {
    /**
     * @var Container
     */
    protected $container;

    /**
     * @param Container $container
     */
    public function setContainer(Container $container) {
        $this->container = $container;
    }

    public function execute(InputInterface $input, OutputInterface $output) {
        try {
            $this->safeExecute($input, $output);
        } catch (\Exception $e) {
            $output->writeln($e->getMessage());
        }
    }

    abstract protected function safeExecute(InputInterface $input, OutputInterface $output);
}