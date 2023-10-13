<?php

namespace Application\Dto;

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
}
