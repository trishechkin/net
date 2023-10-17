<?php

namespace Application\Dto;

use Domain\Entity\User;

class UserDto
{
    public function __construct(
        public readonly string $id,
        public readonly string $firstName,
        public readonly string $secondName,
        public readonly string $birthDate,
        public readonly int $age,
        public readonly string $city,
        public readonly string $biography
    )
    {
    }

    public static function makeFromUser(User $user): self
    {
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
