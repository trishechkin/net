<?php

namespace Infrastructure\Provider;

use DI\Container;
use Infrastructure\Support\CommandMap;
use Infrastructure\Support\ServiceProviderInterface;

class AppProvider implements ServiceProviderInterface
{
    public function register(Container $container): void
    {
        $container->set(CommandMap::class, function () {
            return new CommandMap();
        });
    }
}
