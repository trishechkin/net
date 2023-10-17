<?php

namespace Application;

use Application\Dto\UserDto;
use Application\Dto\UserSearchDto;
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

        return UserDto::makeFromUser($user);
    }

    /**
     * @return UserDto[]
     */
    public function search(UserSearchDto $userSearchDto): array
    {
        $userList = $this->userRepository->search(
            prefixFirstName: $userSearchDto->prefixFirstName,
            prefixSecondName: $userSearchDto->prefixSecondName
        );

        $userDtoList = [];
        foreach ($userList as $user) {
            $userDtoList[] = UserDto::makeFromUser($user);
        }

        return $userDtoList;
    }
}
