<?php

use DI\Container;
use Domain\Exception\EntityNotFoundException;
use Domain\Exception\UnauthorizedException;
use Domain\Repository\UserRepositoryInterface;
use Infrastructure\Controller\Api\v1\UserController;
use Infrastructure\Repository\Postgres\UserPostgresRepository;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$container = new Container();

$container->set(UserRepositoryInterface::class, function () {
    return new UserPostgresRepository();
});

$app = AppFactory::createFromContainer($container);

$app->addRoutingMiddleware();

$customErrorHandler = function (
    ServerRequestInterface $request,
    Throwable $exception,
    bool $displayErrorDetails,
    bool $logErrors,
    bool $logErrorDetails,
    ?LoggerInterface $logger = null
) use ($app) {
    $code = 0;
    if ($exception instanceof EntityNotFoundException) {
        $code = 404;
    }

    if ($exception instanceof UnauthorizedException) {
        $code = 401;
    }

    if ($code === 0) {
        $code = $exception->getCode();
    }

    $payload = ['error' => $exception->getMessage()];

    $response = $app->getResponseFactory()->createResponse();
    $response->getBody()->write(
        json_encode($payload, JSON_UNESCAPED_UNICODE)
    );

    return $response->withStatus($code);
};

$errorMiddleware = $app->addErrorMiddleware(true, true, true);
$errorMiddleware->setDefaultErrorHandler($customErrorHandler);

$app->get('/user/get/{id}', [UserController::class, 'get']);

$app->post('/user/register', [UserController::class, 'register']);

$app->post('/login', [UserController::class, 'login']);

// Run app
$app->run();
