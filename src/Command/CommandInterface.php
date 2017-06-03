<?php

namespace App\Command;

use Pimple\Container;

interface CommandInterface {

    /**
     * @param Container $container
     */
    public function setContainer(Container $container);
}