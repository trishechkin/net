<?php

namespace Domain\Entity;

class User
{
    public function __construct(
        public readonly string $firstName,
        public readonly string $secondName,
        public readonly string $birthDate,
        public readonly string $biography,
        public readonly string $city,
        public readonly string $uuid
    )
    {
    }
}
