<?php

namespace App\Service\Upload;

use App\Exception;

class AmazonS3Uploader implements UploaderInterface {

    /**
     * @param array $credentials
     * @throws Exception\AuthException
     */
    public function auth(array $credentials) {
        // TODO: Implement upload() method.
    }

    /**
     * @param $file
     * @throws Exception\RunTimeException
     */
    public function upload($file) {
        // TODO: Implement upload() method.
    }
}