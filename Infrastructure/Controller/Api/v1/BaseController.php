<?php

namespace Infrastructure\Controller\Api\v1;

use JsonException;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpBadRequestException;

class BaseController
{
    /**
     * @throws JsonException
     */
    protected function buildRequestObjectFromBody(ServerRequestInterface $request, array $paramList): object
    {
        $requestObject = json_decode($request->getBody()->getContents(), false, 512, JSON_THROW_ON_ERROR);

        foreach ($paramList as $param) {
            if (property_exists($requestObject, $param) === false) {
                throw new HttpBadRequestException($request, "Отсутствует обязательное поле [$param]");
            }
        }

        return $requestObject;
    }
}
