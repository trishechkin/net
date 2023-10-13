<?php

namespace Application;

use Application\Dto\UserLoginDto;
use Application\Dto\UserRegisterDto;
use Domain\Entity\User;
use Domain\Exception\EntityNotFoundException;
use Domain\Exception\UnauthorizedException;
use Domain\Repository\UserRepositoryInterface;
use Domain\VO\UserId;
use Ramsey\Uuid\Uuid;

class AuthService
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    )
    {
    }

    public function register(UserRegisterDto $userRegisterDto): string
    {
        $user = new User(
            firstName:  $userRegisterDto->firstName,
            secondName: $userRegisterDto->secondName,
            birthDate: $userRegisterDto->birthDate,
            biography: $userRegisterDto->biography,
            city: $userRegisterDto->city,
            uuid: UserId::make()->getValue()
        );

        return $this->userRepository->save(
            user: $user,
            hashPassword: $this->getHashPassword($userRegisterDto->password),
            token: Uuid::uuid4()->toString()
        )->uuid;
    }

    /**
     * @throws EntityNotFoundException
     */
    public function login(UserLoginDto $userLoginDto): string
    {
        $token = $this->userRepository->findToken(
            UserId::make($userLoginDto->id),
            $this->getHashPassword($userLoginDto->password)
        );

        if ($token === null) {
            throw new EntityNotFoundException("Пользователь [$userLoginDto->id] не найден.");
        }

        return $token;
    }

    /**
     * @throws UnauthorizedException
     */
    public function authenticate(string $token): void
    {
        $exist = $this->userRepository->existToken($token);

        if ($exist === false) {
            throw UnauthorizedException::withToken($token);
        }
    }

    private function getHashPassword(string $password): string
    {
        return md5($password);
    }
}
