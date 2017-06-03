#!/usr/bin/env php

<?php

/** @var App\Application $app */
$app = require_once __DIR__ . '/app/bootstrap.php';

$app->setName('Image Processor Bot');

$app->addCommand(new App\Command\Image\ScheduleCommand());
$app->addCommand(new App\Command\Image\ResizeCommand());
$app->addCommand(new App\Command\Image\UploadCommand());
$app->addCommand(new App\Command\Image\StatusCommand());
$app->addCommand(new App\Command\Image\RetryCommand());

$app->run();