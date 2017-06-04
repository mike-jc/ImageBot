<?php

namespace App\Service\Upload;

use App\Exception;
use Aws\S3\S3Client;

class AmazonS3Uploader implements UploaderInterface {

    const DEFAULT_BUCKET = 'default-bucket';

    /**
     * @var S3Client
     */
    private $s3Client;
    /**
     * @var string
     */
    private $region;
    /**
     * @var string
     */
    private $bucketName;

    /**
     * @param array $credentials
     * @param array $parameters
     * @throws Exception\AuthException
     */
    public function init(array $credentials, array $parameters = []) {
        if (empty($credentials['key']) || empty($credentials['secret'])) {
            throw new Exception\AuthException('You must provide credentials (key and secret) for Amazon S3 service');
        }

        $this->region = !empty($credentials['region']) ? $credentials['region'] : null;
        $this->bucketName = !empty($credentials['bucket-name']) ? $credentials['bucket-name'] : self::DEFAULT_BUCKET;
        $this->s3Client = new S3Client([
            'region' => $this->region,
            'version' => 'latest',
            'profile' => 'default',
            'credentials' => [
                'key'    => $credentials['key'],
                'secret' => $credentials['secret'],
            ]
        ]);
    }

    /**
     * @param $file
     * @throws Exception\RunTimeException
     */
    public function upload($file) {
        $this->createBucketIfNeeded();
        $this->s3Client->putObject([
            'Bucket' => $this->bucketName,
            'Key' => basename($file),
            'SourceFile' => $file,
        ]);
    }

    protected function createBucketIfNeeded() {
        $result = $this->s3Client->listBuckets();
        $bucketExists = false;
        foreach ($result['Buckets'] as $bucket) {
            if ($bucket['Name'] == $this->bucketName) {
                $bucketExists = true;
                break;
            }
        }

        if (!$bucketExists) {
            $this->s3Client->createBucket([
                'Bucket' => $this->bucketName,
            ]);
            $this->s3Client->waitUntil('BucketExists', [
                'Bucket' => $this->bucketName,
            ]);
        }
    }
}