<?php

namespace Domain\Repository;

use Domain\Entity\User;
use Domain\VO\UserId;

interface UserRepositoryInterface
{
    public function find(UserId $userId): ?User;

    public function findToken(UserId $userId, string $hashPassword): ?string;

    public function existToken(string $token): bool;

    public function save(User $user, string $hashPassword, string $token): User;

    public function search(string $prefixFirstName, string $prefixSecondName): array;
}
