<?php

namespace App\Service\Upload;

use App\Exception;

interface ManagerInterface {

    /**
     * @param array $config
     * @throws Exception\ConfigException
     */
    public function __construct(array $config);

    /**
     * @param $file
     * @return string
     */
    public function upload($file);
}