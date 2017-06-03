<?php

namespace App\Service\Queue;

use App\Exception;

class Manager implements ManagerInterface {

    const DEFAULT_RESIZE_QUEUE = 'resize';
    const DEFAULT_UPLOAD_QUEUE = 'upload';
    const DEFAULT_DONE_QUEUE = 'done';
    const DEFAULT_FAILED_QUEUE = 'failed';

    /**
     * @var array
     */
    private $queues;

    /**
     * @param array $config
     */
    public function __construct(array $config) {
        $this->queues['resize'] = !empty($config['resize']) ? $config['resize'] : self::DEFAULT_RESIZE_QUEUE;
        $this->queues['upload'] = !empty($config['upload']) ? $config['upload'] : self::DEFAULT_UPLOAD_QUEUE;
        $this->queues['done'] = !empty($config['done']) ? $config['done'] : self::DEFAULT_DONE_QUEUE;
        $this->queues['failed'] = !empty($config['failed']) ? $config['failed'] : self::DEFAULT_FAILED_QUEUE;
    }

    public function getResizeQueue() {
        return $this->queues['resize'];
    }

    public function getUploadQueue() {
        return $this->queues['upload'];
    }

    public function getDoneQueue() {
        return $this->queues['done'];
    }

    public function getFailedQueue() {
        return $this->queues['failed'];
    }

    /**
     * @return array
     */
    public function getAllQueues() {
        return $this->queues;
    }

    /**
     * @param $queue
     * @param int $limit
     * @return array
     */
    public function getFromQueue($queue, $limit = 0) {
        return [];
    }

    /**
     * @param $queue
     * @return int
     */
    public function getQueueLength($queue) {
        return 0;
    }

    /**
     * @param $queue
     * @param array $items
     * @return int
     * @throws Exception\RunTimeException
     */
    public function putToQueue($queue, $items) {
        return 0;
    }
}