<?php

namespace Application;

use Application\Dto\UserDto;
use Domain\Exception\EntityNotFoundException;
use Domain\Repository\UserRepositoryInterface;
use Domain\VO\UserId;

class UserService
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    )
    {
    }

    /**
     * @throws EntityNotFoundException
     */
    public function get(string $guid): UserDto
    {
        $user = $this->userRepository->find(UserId::make($guid));

        if ($user === null) {
            throw new EntityNotFoundException("Пользователь [$guid] не найден");
        }

        $now = new \DateTime();
        $birthDate = \DateTime::createFromFormat('Y-m-d', $user->birthDate);
        $interval = $now->diff($birthDate);

        return new UserDto(
            id: $user->uuid,
            firstName: $user->firstName,
            secondName: $user->secondName,
            birthDate: $user->birthDate,
            age: $interval->y,
            city: $user->city,
            biography: $user->biography
        );
    }
}
