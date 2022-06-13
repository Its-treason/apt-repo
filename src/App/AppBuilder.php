<?php

namespace ItsTreason\AptRepo\App;

use DI\Bridge\Slim\Bridge;
use DI\Container;
use DI\ContainerBuilder;
use ItsTreason\AptRepo\Api\PackageDownload\PackageDownloadController;
use ItsTreason\AptRepo\Api\Packages\PackagesController;
use ItsTreason\AptRepo\Api\PublicKey\PublicKeyController;
use ItsTreason\AptRepo\Api\Release\InReleaseController;
use ItsTreason\AptRepo\Api\Release\ReleaseController;
use ItsTreason\AptRepo\Api\Release\ReleaseGpgController;
use ItsTreason\AptRepo\Api\UploadPackage\UploadPackageActionController;
use ItsTreason\AptRepo\Api\UploadPackage\UploadPackageFormController;
use ItsTreason\AptRepo\App\Factory\PdoFactory;
use ItsTreason\AptRepo\App\Factory\TwigFactory;
use ItsTreason\AptRepo\App\Factory\UplinkFactory;
use ItsTreason\AptRepo\App\Middleware\ErrorMiddleware;
use PDO;
use Slim\App;
use Storj\Uplink\Project;
use Twig\Environment;
use function DI\factory;

class AppBuilder
{
    public function build(): App
    {
        $container = $this->createContainer();

        $app = Bridge::create($container);

        $app->addRoutingMiddleware();

        $this->addErrorMiddleware($app);
        $this->addAppRoutes($app);

        return $app;
    }

    private function addAppRoutes(App $app): void
    {
        // TODO: Auth middleware
        $app->get('/ui/upload', UploadPackageFormController::class);
        $app->post('/ui/upload', UploadPackageActionController::class);

        $app->get('/ui/pgp', PublicKeyController::class);

        $app->get('/pool/main/{filename}', PackageDownloadController::class);

        $app->get('/dists/stable/Release', ReleaseController::class);
        $app->get('/dists/stable/InRelease', InReleaseController::class);
        $app->get('/dists/stable/Release.gpg', ReleaseGpgController::class);
        $app->get(
            '/dists/stable/main/{arch}/{package}',
            PackagesController::class,
        );
    }

    private function createContainer(): Container
    {
        $builder = new ContainerBuilder();

        $builder->addDefinitions([
            PDO::class => factory(PdoFactory::class),
            Environment::class => factory(TwigFactory::class),
            Project::class => factory(UplinkFactory::class),
        ]);

        return $builder->build();
    }

    private function addErrorMiddleware(App $app): void
    {
        $errorMiddleware = $app->addErrorMiddleware(true, true, true);
        $errorMiddleware = $errorMiddleware->getDefaultErrorHandler();
        $errorMiddleware->registerErrorRenderer('text/html', ErrorMiddleware::class);
    }
}
