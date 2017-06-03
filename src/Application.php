<?php

namespace App;

use App\Command\BaseCommand;
use Pimple\Container;
use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;

class Application extends BaseApplication {
    /**
     * @var Container;
     */
    private $container;

    /**
     * @param Container $container
     * @param string $name
     * @param string $version
     */
    public function __construct(Container $container, $name = 'UNKNOWN', $version = 'UNKNOWN') {
        parent::__construct($name, $version);
        $this->container = $container;
    }

    /**
     * @param BaseCommand $command
     * @return null|\Symfony\Component\Console\Command\Command
     */
    public function addCommand(BaseCommand $command) {
        $command->setContainer($this->container);
        return parent::add($command);
    }

    /**
     * @return InputDefinition
     */
    protected function getDefaultInputDefinition() {
        return new InputDefinition([
            new InputArgument('command', InputArgument::REQUIRED, 'The command to execute'),
            new InputOption('--help', '-h', InputOption::VALUE_NONE, 'Display this help message'),
        ]);
    }
}