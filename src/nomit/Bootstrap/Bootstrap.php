<?php

namespace nomit\Bootstrap;

use nomit\Kernel\Environment\EnvironmentVariableLoader;

class Bootstrap
{

    public static function boot(): Configurator
    {
        $root = dirname(__DIR__, 3) . DIRECTORY_SEPARATOR;
        $configurator = new Configurator($root);

        $configurator->setRoot($root);

        $configurator->createClassLoader()
            ->addDirectory($root . 'src' . DIRECTORY_SEPARATOR . 'nomit' . DIRECTORY_SEPARATOR)
            ->addDirectory($root . 'src' . DIRECTORY_SEPARATOR . 'Psr' . DIRECTORY_SEPARATOR)
            ->addDirectory($root . 'src' . DIRECTORY_SEPARATOR . 'Dependencies' . DIRECTORY_SEPARATOR)
            ->addDirectory($root . 'app' . DIRECTORY_SEPARATOR)
            ->register();

        $configurator->addConfiguration($root . 'config' . DIRECTORY_SEPARATOR . 'config.neon');

        return $configurator;
    }

}