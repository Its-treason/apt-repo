<?php

namespace ItsTreason\AptRepo\App\Factory;

use Monolog\Logger;
use PDO;
use DI\Container;
use DI\ContainerBuilder as DIContainerBuilder;
use ItsTreason\AptRepo\FileStorage\FileStorageInterface;
use ItsTreason\AptRepo\FileStorage\FileStorageFactory;
use Storj\Uplink\Project;
use Twig\Environment;
use function DI\factory;

class ContainerFactory
{
    public static function buildContainer(): Container
    {
        $builder = new DIContainerBuilder();

        $builder->addDefinitions([
            PDO::class => factory(PdoFactory::class),
            Environment::class => factory(TwigFactory::class),
            Project::class => factory(UplinkFactory::class),
            FileStorageInterface::class => factory(FileStorageFactory::class),
            Logger::class => factory(LoggerFactory::class),
        ]);

        return $builder->build();
    }
}
