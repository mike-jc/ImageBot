<?php

namespace App\Service\Upload;

use App\Exception;

class GDriveUploader implements UploaderInterface {

    /**
     * @param array $parameters
     * @throws Exception\ConfigException
     */
    public function init(array $parameters = []) {
        // TODO: Implement init() method.
    }

    /**
     * @param array $credentials
     * @throws Exception\AuthException
     */
    public function auth(array $credentials) {
        // TODO: Implement auth() method.
    }

    /**
     * @param $file
     * @throws Exception\RunTimeException
     */
    public function upload($file) {
        // TODO: Implement upload() method.
    }
}