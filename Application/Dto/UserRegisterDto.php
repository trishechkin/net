<?php

namespace Application\Dto;

class UserRegisterDto
{
    public function __construct(
        public readonly string $firstName,
        public readonly string $secondName,
        public readonly string $birthDate,
        public readonly string $city,
        public readonly string $biography,
        public readonly string $password
    )
    {
    }

    public static function getFieldList(): array
    {
        return [
            'firstName',
            'secondName',
            'birthDate',
            'city',
            'biography',
            'password'
        ];
    }
}
