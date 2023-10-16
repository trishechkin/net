<?php

namespace Infrastructure\Support;

use DI\Container;

interface ServiceProviderInterface
{
    public function register(Container $container): void;
}
