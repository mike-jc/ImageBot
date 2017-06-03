<?php

namespace App\Service\Resize;

use App\Exception;

interface ResizerInterface {

    const DEFAULT_SIZE = 640;
    const DEFAULT_COLOR= '#000000';

    /**
     * @param array $config
     * @throws Exception\ConfigException
     */
    public function __construct(array $config = []);

    /**
     * @param string $file
     * @return string
     * @throws Exception\RunTimeException
     */
    public function resize($file);
}