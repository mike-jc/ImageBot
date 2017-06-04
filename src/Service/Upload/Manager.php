<?php

namespace App\Service\Upload;

use App\Exception;

class Manager implements ManagerInterface {

    const DEFAULT_TYPE = 'amazon-s3';

    /**
     * @var UploaderInterface
     */
    private $uploader;

    /**
     * @param array $config
     * @throws Exception\ConfigException
     */
    public function __construct(array $config) {
        $type = !empty($config['type']) ? $config['type'] : self::DEFAULT_TYPE;
        $parameters = !empty($config['parameters']) ? $config['parameters'] : [];
        $credentials = !empty($config['credentials']) ? $config['credentials'] : [];

        if (!$credentials) {
            throw new Exception\ConfigException("Empty credentials to run authentication at storage");
        }

        $this->uploader = $this->initUploader($type);
        $this->uploader->init($credentials, $parameters);
    }

    /**
     * @param $file
     * @return string
     */
    public function upload($file) {
        $this->uploader->upload($file);
        return $file;
    }

    /**
     * @param string $type
     * @return UploaderInterface
     * @throws Exception\ConfigException
     */
    private function initUploader($type) {
        switch ($type) {
            case 'amazon-s3':
                return new AmazonS3Uploader();
            case 'g-drive':
                return new GDriveUploader();
            case 'dropbox':
                return new DropboxUploader();
            default:
                throw new Exception\ConfigException("[CONFIG] Current type for storage is unsupported. Use one of `amazon-s3`, `g-drive`, `dropbox`");
        }
    }
}