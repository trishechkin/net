<?php

require __DIR__ . '/vendor/autoload.php';

use DI\Container;
use Infrastructure\Provider\AppProvider;
use Infrastructure\Provider\CommandProvider;
use Infrastructure\Support\ServiceProviderInterface;

$providerList = [
    AppProvider::class,
    CommandProvider::class
];

$container = new Container();

// Регистрация сервисов
foreach ($providerList as $className) {
    if (class_exists($className) === false) {
        throw new \RuntimeException("Provider [$className] not found.");
    }

    $provider = new $className;

    if (($provider instanceof ServiceProviderInterface) === false) {
        throw new RuntimeException("$className has not provider");
    }

    $provider->register($container);
}

return $container;
