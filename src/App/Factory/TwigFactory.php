<?php

namespace ItsTreason\AptRepo\App\Factory;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class TwigFactory
{
    public function __invoke(): Environment
    {
        $loader = new FilesystemLoader(__DIR__ . '/../../Views');

        $debug = false;
        if (APP_ENV === 'development') {
            $debug = true;
        }

        return new Environment($loader, ['debug' => $debug]);
    }
}
