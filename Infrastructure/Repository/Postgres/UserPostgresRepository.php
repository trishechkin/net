<?php

namespace Infrastructure\Repository\Postgres;

use Domain\Entity\User;
use Domain\Repository\UserRepositoryInterface;
use Domain\VO\UserId;

class UserPostgresRepository extends PostgresConnectionManager implements UserRepositoryInterface
{
    public function find(UserId $userId): ?User
    {
        $query = 'SELECT * FROM "user" WHERE guid = :guid';

        $param = [
            'guid' => $userId->getValue(),
        ];

        $statement = $this->connection->prepare($query);
        $statement->execute($param);

        $user = $statement->fetch(\PDO::FETCH_OBJ);

        if ($user === false) {
            return null;
        }

        return new User(
            firstName: $user->first_name,
            secondName: $user->second_name,
            birthDate: $user->birth_date,
            biography: $user->biography,
            city: $user->city,
            uuid: $user->guid
        );
    }

    public function findToken(UserId $userId, string $hashPassword): ?string
    {
        $query = 'SELECT token FROM "user" WHERE guid = :guid AND password = :password';

        $param = [
            'guid' => $userId->getValue(),
            'password' => $hashPassword,
        ];

        $statement = $this->connection->prepare($query);
        $statement->execute($param);

        $user = $statement->fetch(\PDO::FETCH_OBJ);

        if ($user === false) {
            return null;
        }

        return $user->token;
    }

    public function save(User $user, string $hashPassword, string $token): User
    {
        $queryInsert = 'INSERT INTO "user"(guid, first_name, second_name, city, birth_date, biography, password, token) '
            . 'VALUES (:guid, :first_name, :second_name, :city, :birth_date, :biography, :password, :token)';

        $paramInsert = [
            'guid' => $user->uuid,
            'first_name' => $user->firstName,
            'second_name' => $user->secondName,
            'city' => $user->city,
            'birth_date' => $user->birthDate,
            'biography' => $user->biography,
            'password' => $hashPassword,
            'token' => $token
        ];

        $statement = $this->connection->prepare($queryInsert);
        $statement->execute($paramInsert);

        return $user;
    }

    public function existToken(string $token): bool
    {
        $query = 'SELECT token FROM "user" WHERE token = :token';

        $param = [
            'token' => $token,
        ];

        $statement = $this->connection->prepare($query);
        $statement->execute($param);

        $exist = $statement->fetch(\PDO::FETCH_OBJ);

        return $exist !== false;
    }
}
