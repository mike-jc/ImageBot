<?php

namespace App\Service\Upload;

use App\Exception;

class GDriveUploader implements UploaderInterface {
    /**
     * @var \Google_Client
     */
    private $gClient;
    /**
     * @var \Google_Service_Drive
     */
    private $gDrive;

    /**
     * @param array $credentials
     * @param array $parameters
     * @throws Exception\AuthException
     */
    public function init(array $credentials, array $parameters = []) {
        if (empty($credentials['config-file'])) {
            throw new Exception\AuthException('You must provide path to file with credentials for service account at Google API Console');
        }

        $this->gClient = new \Google_Client();
        $this->gClient->setAuthConfig($credentials['config-file']);
        $this->gClient->setAccessType("offline");
        $this->gClient->addScope(\Google_Service_Drive::DRIVE_FILE);

        $this->gDrive = new \Google_Service_Drive($this->gClient);
    }

    /**
     * @param string $file
     */
    public function upload($file) {
        $metadata = new \Google_Service_Drive_DriveFile([
            'name' => basename($file),
        ]);
        $this->gDrive->files->create($metadata, [
            'data' => file_get_contents($file),
            'uploadType' => 'media',
        ]);
    }
}