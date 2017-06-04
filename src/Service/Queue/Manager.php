<?php

namespace App\Service\Queue;

use App\Exception;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;

class Manager implements ManagerInterface {

    const DEFAULT_RESIZE_QUEUE = 'resize';
    const DEFAULT_UPLOAD_QUEUE = 'upload';
    const DEFAULT_DONE_QUEUE = 'done';
    const DEFAULT_FAILED_QUEUE = 'failed';

    /**
     * @var AMQPStreamConnection
     */
    private $mqConnection;
    /**
     * @var AMQPChannel
     */
    private $mqChannel;
    /**
     * @var array
     */
    private $queues;

    /**
     * @param array $config
     * @throws Exception\RunTimeException
     */
    public function __construct(array $config) {
        if (empty($config['host']) || empty($config['port']) || empty($config['user']) || empty($config['password'])) {
            throw new Exception\RunTimeException('You must specify connection parameters for RabbitMQ');
        }

        $this->queues['resize'] = !empty($config['names']['resize']) ? $config['names']['resize'] : self::DEFAULT_RESIZE_QUEUE;
        $this->queues['upload'] = !empty($config['names']['upload']) ? $config['names']['upload'] : self::DEFAULT_UPLOAD_QUEUE;
        $this->queues['done'] = !empty($config['names']['done']) ? $config['names']['done'] : self::DEFAULT_DONE_QUEUE;
        $this->queues['failed'] = !empty($config['names']['failed']) ? $config['names']['failed'] : self::DEFAULT_FAILED_QUEUE;

        $this->mqConnection = new AMQPStreamConnection($config['host'], $config['port'], $config['user'], $config['password']);
        $this->mqChannel = $this->mqConnection->channel();

        foreach ($this->queues as $queue) {
            $this->declareQueue($queue);
        }
    }

    public function __destruct() {
        $this->mqChannel->close();
        $this->mqConnection->close();
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
        $i = 0 ;
        $items = [];
        do {
            $i++;

            /** @var AMQPMessage $msg */
            $msg = $this->mqChannel->basic_get($queue);
            if ($msg) {
                $this->mqChannel->basic_ack($msg->delivery_info['delivery_tag']);

                $properties = $msg->get_properties();
                $isJson = !empty($properties['content_type']) && $properties['content_type'] == 'application/json';
                $items[] = $isJson ? json_decode($msg->getBody()) : $msg->getBody();
            }
        } while ($msg && (!$limit || $i < $limit));

        return $items;
    }

    /**
     * @param $queue
     * @return int|null
     */
    public function getQueueLength($queue) {
        $info = $this->declareQueue($queue);
        return isset($info[1]) ? $info[1] : null;
    }

    /**
     * @param $queue
     * @param array $items
     * @return int
     * @throws Exception\RunTimeException
     */
    public function putToQueue($queue, $items) {
        $successful = 0;
        foreach ((array)$items as $item) {
            if (is_object($item) || is_array($item)) {
                $msg = new AMQPMessage(json_encode($item), [
                    'content_type'  => 'application/json',
                    'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
                ]);
            } else {
                $msg = new AMQPMessage($item, [
                    'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
                ]);
            }
            $this->mqChannel->basic_publish($msg, '', $queue);
            $successful++;
        }
        return $successful;
    }

    /**
     * @param $queue
     * @return array
     */
    private function declareQueue($queue) {
        return $this->mqChannel->queue_declare($queue, false, true, false, false);
    }
}