<?php

namespace App\Service\Upload;

use App\Exception;

interface UploaderInterface {

    /**
     * @param array $credentials
     * @throws Exception\AuthException
     */
    public function auth(array $credentials);

    /**
     * @param $file
     * @throws Exception\RunTimeException
     */
    public function upload($file);
}