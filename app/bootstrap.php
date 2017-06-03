<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Application;
use App\Exception\ConfigException;
use App\Service\Queue\Manager as QueueManager;
use App\Service\Resize\ResizerService;
use App\Service\Upload\Manager as UploadManager;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Pimple\Container;
use Symfony\Component\Yaml\Yaml;

define('APP_NAME',    'PHP CLI application');
define('APP_VERSION', '1.0.0');
define('DEBUG',       false);
define('CONFIG',      __DIR__ . '/../config/config.yml');
define('LOG_DIR',     __DIR__ . '/../logs/');
define('LOG',         LOG_DIR . 'app.log');

$container = new Container();
$application = new Application($container, APP_NAME, APP_VERSION);

/*
 * Get configuration
 */

try {
    $config = Yaml::parse(file_get_contents(CONFIG));

    if ($config === CONFIG) {
        throw new ConfigException('The configuration file was not found. Aborting.');
    }
    if (!is_writable(LOG_DIR))
        throw new ConfigException('The logs directory is not writable. Aborting.');

} catch (\Exception $exception) {
    $application->renderException(
        $exception,
        new Symfony\Component\Console\Output\ConsoleOutput()
    );
    exit(-1);
}

$container['config'] = $config ?: [];

/*
 * Define services
 */

$container['logger'] = function() {
    $logger = new Logger('app');
    $logger->pushHandler(new StreamHandler(LOG));
    return $logger;
};

$container['queue-manager'] = function ($c) {
    $queueConfig = !empty($c['config']['queues']) ? $c['config']['queues'] : [];
    return new QueueManager($queueConfig);
};

$container['resizer'] = function ($c) {
    $resizerConfig = !empty($c['config']['resizer']) ? $c['config']['resizer'] : [];
    return new ResizerService($resizerConfig);
};

$container['upload-manager'] = function ($c) {
    $storageConfig = !empty($c['config']['storage']) ? $c['config']['storage'] : [];
    return new UploadManager($storageConfig);
};

return $application;