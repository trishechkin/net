<?php

namespace Infrastructure\Provider;

use Application\AuthService;
use DI\Container;
use Infrastructure\Command\UserLoadCommand;
use Infrastructure\Repository\Postgres\UserPostgresRepository;
use Infrastructure\Support\CommandMap;
use Infrastructure\Support\ServiceProviderInterface;

class CommandProvider implements ServiceProviderInterface
{
    public function register(Container $container): void
    {
        $container->set(UserLoadCommand::class, function () {
            return new UserLoadCommand(new AuthService(
                new UserPostgresRepository()
            ));
        });

        $container->get(CommandMap::class)->set(UserLoadCommand::COMMAND_NAME, UserLoadCommand::class);
    }
}
