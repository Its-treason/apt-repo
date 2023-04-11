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
use ItsTreason\AptRepo\Api\Ui\FileList\ArchFileListController;
use ItsTreason\AptRepo\Api\Ui\FileList\ComponentFileListController;
use ItsTreason\AptRepo\Api\Ui\FileList\DistsFileListController;
use ItsTreason\AptRepo\Api\Ui\FileList\PoolComponentFileListController;
use ItsTreason\AptRepo\Api\Ui\FileList\PoolFileListController;
use ItsTreason\AptRepo\Api\Ui\FileList\RootFileListController;
use ItsTreason\AptRepo\Api\Ui\FileList\SuiteFileListController;
use ItsTreason\AptRepo\Api\Ui\Login\LoginActionController;
use ItsTreason\AptRepo\Api\Ui\Login\LoginFormController;
use ItsTreason\AptRepo\Api\Ui\PackageList\PackageDeleteController;
use ItsTreason\AptRepo\Api\Ui\PackageList\PackageDetailController;
use ItsTreason\AptRepo\Api\Ui\PackageList\PackageListController;
use ItsTreason\AptRepo\Api\Ui\PackageList\PackageSuiteAddController;
use ItsTreason\AptRepo\Api\Ui\PackageList\PackageSuiteRemoveController;
use ItsTreason\AptRepo\Api\Ui\RepositoryInfo\RepositoryInfoController;
use ItsTreason\AptRepo\Api\Ui\Suites\CreateSuiteController;
use ItsTreason\AptRepo\Api\Ui\Suites\SuiteListController;
use ItsTreason\AptRepo\Api\Ui\UploadPackage\UploadPackageActionController;
use ItsTreason\AptRepo\Api\Ui\UploadPackage\UploadPackageFormController;
use ItsTreason\AptRepo\App\Factory\PdoFactory;
use ItsTreason\AptRepo\App\Factory\TwigFactory;
use ItsTreason\AptRepo\App\Factory\UplinkFactory;
use ItsTreason\AptRepo\App\Middleware\AuthMiddleware;
use ItsTreason\AptRepo\App\Middleware\ErrorMiddleware;
use ItsTreason\AptRepo\FileStorage\FileStorageFactory;
use ItsTreason\AptRepo\FileStorage\FileStorageInterface;
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
        $app->get('/ui[/]', RepositoryInfoController::class);

        $app->get('/ui/upload', UploadPackageFormController::class)->add(AuthMiddleware::class);
        $app->post('/ui/upload', UploadPackageActionController::class)->add(AuthMiddleware::class);

        $app->get('/ui/suites', SuiteListController::class);
        $app->post('/ui/suites/create', CreateSuiteController::class)->add(AuthMiddleware::class);
        $app->delete('/ui/suites/create', CreateSuiteController::class)->add(AuthMiddleware::class);

        $app->get('/ui/login', LoginFormController::class);
        $app->post('/ui/login', LoginActionController::class);

        $app->get('/ui/packages[/]', PackageListController::class);
        $app->get('/ui/packages/{packageName}', PackageDetailController::class);
        $app->post('/ui/packages/{packageName}/delete', PackageDeleteController::class)->add(AuthMiddleware::class);
        $app->post('/ui/packages/{packageName}/addSuite', PackageSuiteAddController::class)->add(AuthMiddleware::class);
        $app->post('/ui/packages/{packageName}/removeSuite', PackageSuiteRemoveController::class)->add(AuthMiddleware::class);

        $app->get('/ui/pgp', PublicKeyController::class);

        $app->get('/dists/{codename}/Release', ReleaseController::class);
        $app->get('/dists/{codename}/InRelease', InReleaseController::class);
        $app->get('/dists/{codename}/Release.gpg', ReleaseGpgController::class);
        $app->get(
            '/dists/{codename}/{suite}/{arch}/{package}',
            PackagesController::class,
        );

        $app->get('/', RootFileListController::class);
        $app->get('/dists[/]', DistsFileListController::class);
        $app->get('/dists/{codename}[/]', ComponentFileListController::class);
        $app->get('/dists/{codename}/{suite}[/]', SuiteFileListController::class);
        $app->get('/dists/{codename}/{suite}/{arch}[/]', ArchFileListController::class);
        $app->get('/pool[/]', PoolComponentFileListController::class);
        $app->get('/pool/main[/]', PoolFileListController::class);
        $app->get('/pool/main/{filename}', PackageDownloadController::class);
    }

    private function createContainer(): Container
    {
        $builder = new ContainerBuilder();

        $builder->addDefinitions([
            PDO::class => factory(PdoFactory::class),
            Environment::class => factory(TwigFactory::class),
            Project::class => factory(UplinkFactory::class),
            FileStorageInterface::class => factory(FileStorageFactory::class),
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
