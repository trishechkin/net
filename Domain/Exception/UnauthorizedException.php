<?php

namespace Domain\Exception;

use Exception;

class UnauthorizedException extends Exception
{
    public static function withToken(string $token): static
    {
        return new static("Пользователь [$token] не найден.");
    }
}
