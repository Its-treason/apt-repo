<?php

namespace ItsTreason\AptRepo;

require __DIR__ . '/../vendor/autoload.php';

use ItsTreason\AptRepo\App\AppBuilder;
use ItsTreason\AptRepo\App\Factory\ContainerFactory;
use ItsTreason\AptRepo\Command\Cron\UpdateGitHubSubsctionsCommand;
use ItsTreason\AptRepo\Command\Debug\RegeneratePackageLists;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\CommandLoader\ContainerCommandLoader;

define('APP_ENV', getenv('APP_ENV'));

if (php_sapi_name() === 'cli') {
    $container = ContainerFactory::buildContainer();
    $commandLoader = new ContainerCommandLoader($container, [
        UpdateGitHubSubsctionsCommand::NAME => UpdateGitHubSubsctionsCommand::class,
        RegeneratePackageLists::NAME => RegeneratePackageLists::class,
    ]);

    $app = new Application();
    $app->setCommandLoader($commandLoader);
    $code = $app->run();
    die($code);
}

$appBuilder = new AppBuilder();
$app = $appBuilder->build();
$app->run();
