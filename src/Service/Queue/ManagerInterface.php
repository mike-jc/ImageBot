<?php

namespace App\Service\Queue;

use App\Exception;

interface ManagerInterface {

    /**
     * @param array $config
     */
    public function __construct(array $config);

    public function getResizeQueue();

    public function getUploadQueue();

    public function getDoneQueue();

    public function getFailedQueue();

    /**
     * @return array
     */
    public function getAllQueues();

        /**
     * @param $queue
     * @param int $limit
     * @return array
     */
    public function getFromQueue($queue, $limit = 0);

    /**
     * @param $queue
     * @return int
     */
    public function getQueueLength($queue);

    /**
     * @param $queue
     * @param array $items
     * @return int
     * @throws Exception\RunTimeException
     */
    public function putToQueue($queue, $items);
}