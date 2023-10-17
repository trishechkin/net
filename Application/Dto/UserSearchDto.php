<?php

namespace Application\Dto;

class UserSearchDto
{
    public function __construct(
        public readonly string $prefixFirstName,
        public readonly string $prefixSecondName
    )
    {
    }

    public static function getFieldList(): array
    {
        return [
            'prefix_second_name',
            'prefix_second_name',
        ];
    }
}
