<?php

namespace ItsTreason\AptRepo;

use ItsTreason\AptRepo\App\AppBuilder;
use ItsTreason\AptRepo\App\Factory\ContainerFactory;
use ItsTreason\AptRepo\App\Command\UpdateGitHubSubsctionsCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\CommandLoader\ContainerCommandLoader;

require __DIR__ . '/../vendor/autoload.php';

define('APP_ENV', getenv('APP_ENV'));

if (php_sapi_name() === 'cli') {
    $container = ContainerFactory::buildContainer();
    $commandLoader = new ContainerCommandLoader($container, [
        UpdateGitHubSubsctionsCommand::NAME => UpdateGitHubSubsctionsCommand::class,
    ]);

    $app = new Application();
    $app->setCommandLoader($commandLoader);
    $code = $app->run();
    die($code);
}

$appBuilder = new AppBuilder();
$app = $appBuilder->build();

$app->run();

