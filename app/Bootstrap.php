<?php

namespace Application\Bootstrap;

use nomit\Bootstrap\Configurator;
use nomit\Kernel\Environment\EnvironmentVariableLoader;

class Bootstrap
{

    public static function boot(string $rootDirectory, string $environment, bool $debug): Configurator
    {
        $configurator = new Configurator($rootDirectory, $environment, $debug);
        $configurator->setRoot($rootDirectory);

        $configurator->createClassLoader()
            ->addDirectory($rootDirectory . 'src' . DIRECTORY_SEPARATOR . 'nomit' . DIRECTORY_SEPARATOR)
            ->addDirectory($rootDirectory . 'src' . DIRECTORY_SEPARATOR . 'Psr' . DIRECTORY_SEPARATOR)
            ->addDirectory($rootDirectory . 'src' . DIRECTORY_SEPARATOR . 'Dependencies' . DIRECTORY_SEPARATOR)
            ->addDirectory($rootDirectory . 'app' . DIRECTORY_SEPARATOR)
            ->register();

        $configurator->addConfiguration($rootDirectory . 'config' . DIRECTORY_SEPARATOR . 'config.neon');

        return $configurator;
    }

}