<?php

namespace Infrastructure\Controller\Api\v1;

use Application\AuthService;
use Application\Dto\UserLoginDto;
use Application\Dto\UserRegisterDto;
use Application\Dto\UserSearchDto;
use Application\UserService;
use JsonException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpBadRequestException;

class UserController extends BaseController
{
    public function __construct(
        private AuthService $authService,
        private UserService $userService
    )
    {
    }

    public function get(ServerRequestInterface $request, ResponseInterface $response, $args): ResponseInterface
    {
        $this->authService->authenticate($request->getHeaderLine('Authorization'));

        if (array_key_exists('id', $args) === true) {
            $userDto = $this->userService->get($args['id']);

            $response->getBody()->write(json_encode([
                'id' => $userDto->id,
                'first_name' => $userDto->firstName,
                'second_name' => $userDto->secondName,
                'age' => $userDto->age,
                'birthdate' => $userDto->birthDate,
                'biography' => $userDto->biography,
                'city' => $userDto->city
            ], JSON_THROW_ON_ERROR));
        } else {
            throw new HttpBadRequestException($request, 'Не указан идентификатор пользователя.');
        }

        return $response->withHeader('Content-Type', 'application/json; charset=UTF-8');
    }

    public function register(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $userRegisterDto = $this->makeUserRegisterDto($request);

        $userId = $this->authService->register($userRegisterDto);

        $response->getBody()->write(json_encode([
            'user_id' => $userId
        ], JSON_THROW_ON_ERROR));

        return $response->withHeader('Content-Type', 'application/json; charset=UTF-8');
    }

    public function login(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $userLoginDto = $this->makeUserLoginDto($request);

        $token = $this->authService->login($userLoginDto);

        $response->getBody()->write(json_encode([
            'token' => $token
        ], JSON_THROW_ON_ERROR));

        return $response->withHeader('Content-Type', 'application/json; charset=UTF-8');
    }

    public function search(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $userSearchDto = $this->makeUserSearchDto($request);

        $userList = $this->userService->search($userSearchDto);

        $responseArray = [];
        foreach ($userList as $user) {
            $responseArray[] = [
                'id' => $user->id,
                'first_name' => $user->firstName,
                'second_name' => $user->secondName,
                'age' => $user->age,
                'birthdate' => $user->birthDate,
                'biography' => $user->biography,
                'city' => $user->city
            ];
        }

        $response->getBody()->write(json_encode($responseArray, JSON_THROW_ON_ERROR));

        return $response->withHeader('Content-Type', 'application/json; charset=UTF-8');
    }

    /**
     * @throws JsonException
     */
    private function makeUserLoginDto(ServerRequestInterface $request): UserLoginDto
    {
        $requestObject = $this->buildRequestObjectFromBody($request, UserLoginDto::getFieldList());

        return new UserLoginDto(
            id: $requestObject->id,
            password: $requestObject->password
        );
    }

    /**
     * @throws JsonException
     */
    private function makeUserRegisterDto(ServerRequestInterface $request): UserRegisterDto
    {
        $requestObject = $this->buildRequestObjectFromBody($request, UserRegisterDto::getFieldList());

        return new UserRegisterDto(
            firstName: $requestObject->firstName,
            secondName: $requestObject->secondName,
            birthDate: $requestObject->birthDate,
            city: $requestObject->city,
            biography: $requestObject->biography,
            password: $requestObject->password
        );
    }

    private function makeUserSearchDto(ServerRequestInterface $request): UserSearchDto
    {
        $queryParamList = $request->getQueryParams();

        foreach (UserSearchDto::getFieldList() as $param) {
            if (array_key_exists($param, $queryParamList) === false) {
                throw new HttpBadRequestException($request, "Отсутствует обязательное поле [$param]");
            }
        }

        return new UserSearchDto(
            prefixFirstName: $queryParamList['prefix_first_name'],
            prefixSecondName: $queryParamList['prefix_second_name']
        );
    }
}
