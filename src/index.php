<?php

namespace ItsTreason\AptRepo;

use ItsTreason\AptRepo\App\AppBuilder;

require __DIR__ . '/../vendor/autoload.php';

define('APP_ENV', getenv('APP_ENV'));

$appBuilder = new AppBuilder();

$app = $appBuilder->build();

$app->run();

