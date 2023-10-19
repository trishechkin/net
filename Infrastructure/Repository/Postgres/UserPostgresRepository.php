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

        $paramList = [
            'guid' => $userId->getValue(),
        ];

        $statement = $this->connection->prepare($query);
        $statement->execute($paramList);

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

        $paramList = [
            'guid' => $userId->getValue(),
            'password' => $hashPassword,
        ];

        $statement = $this->connection->prepare($query);
        $statement->execute($paramList);

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

        $paramList = [
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
        $statement->execute($paramList);

        return $user;
    }

    public function existToken(string $token): bool
    {
        $query = 'SELECT token FROM "user" WHERE token = :token';

        $paramList = [
            'token' => $token,
        ];

        $statement = $this->connection->prepare($query);
        $statement->execute($paramList);

        $exist = $statement->fetch(\PDO::FETCH_OBJ);

        return $exist !== false;
    }

    public function search(string $prefixFirstName, string $prefixSecondName): array
    {
        $query = 'SELECT * FROM "user" WHERE lower(first_name) LIKE :prefix_first_name AND lower(second_name) LIKE :prefix_second_name';

        $paramPrefixFirstName = mb_strtolower($prefixFirstName) . '%';
        $paramPrefixSecondName = mb_strtolower($prefixSecondName) . '%';

        $statement = $this->connection->prepare($query);
        $statement->bindParam('prefix_first_name', $paramPrefixFirstName);
        $statement->bindParam('prefix_second_name', $paramPrefixSecondName);
        $statement->execute();

        $userList = $statement->fetchAll(\PDO::FETCH_OBJ);

        if ($userList === false) {
            return [];
        }

        $result = [];
        foreach ($userList as $user) {
            $result[] =  new User(
                firstName: $user->first_name,
                secondName: $user->second_name,
                birthDate: $user->birth_date,
                biography: $user->biography,
                city: $user->city,
                uuid: $user->guid
            );
        }

        return $result;
    }
}
