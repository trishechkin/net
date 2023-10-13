<?php

namespace Application\Dto;

class UserLoginDto
{
    public function __construct(
        public readonly string $id,
        public readonly string $password
    )
    {
    }

    /**
     * @return string[]
     */
    public static function getFieldList(): array
    {
        return [
            'id',
            'password'
        ];
    }
}
