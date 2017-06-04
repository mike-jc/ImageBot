<?php

namespace App\Service\Upload;

use App\Exception;
use Kunnu\Dropbox\Dropbox;
use Kunnu\Dropbox\DropboxApp;

class DropboxUploader implements UploaderInterface {
    /**
     * @var Dropbox
     */
    private $client;

    /**
     * @param array $credentials
     * @param array $parameters
     * @throws Exception\AuthException
     */
    public function init(array $credentials, array $parameters = []) {
        if (empty($credentials['key']) || empty($credentials['secret']) || empty($credentials['access-token'])) {
            throw new Exception\AuthException('You must provide credentials (key, secret and access token) for Dropbox API');
        }

        $dropboxApp = new DropboxApp($credentials['key'], $credentials['secret'], $credentials['access-token']);
        $this->client = new Dropbox($dropboxApp);
    }

    /**
     * @param string $file
     */
    public function upload($file) {
        $this->client->upload($file, '/'. basename($file), [
            'autorename' => true
        ]);
    }
}