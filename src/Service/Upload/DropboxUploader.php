<?php

namespace App\Service\Upload;

use App\Exception;

class DropboxUploader implements UploaderInterface {

    /**
     * @param array $credentials
     * @param array $parameters
     * @throws Exception\ConfigException
     * @throws Exception\AuthException
     */
    public function init(array $credentials, array $parameters = []) {
        // TODO: Implement init() method.
    }

    /**
     * @param $file
     * @throws Exception\RunTimeException
     */
    public function upload($file) {
        // TODO: Implement upload() method.
    }
}