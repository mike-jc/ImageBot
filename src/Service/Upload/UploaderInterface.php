<?php

namespace App\Service\Upload;

use App\Exception;

interface UploaderInterface {

    /**
     * @param array $credentials
     * @param array $parameters
     * @throws Exception\ConfigException
     * @throws Exception\AuthException
     */
    public function init(array $credentials, array $parameters = []);

    /**
     * @param $file
     * @throws Exception\RunTimeException
     */
    public function upload($file);
}