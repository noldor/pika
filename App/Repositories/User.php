<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\User as UserModel;
use PDO;
use PDOStatement;
use RuntimeException;
use Support\Exceptions\EntityNotFoundException;
use Support\JWT;

class User
{
    private const FIND_BY_ID_QUERY = 'SELECT * from users WHERE id = :id';

    private const FIND_BY_EMAIL_QUERY = 'SELECT * from users WHERE email = :email';

    private const FIND_BY_ACCESS_TOKEN_QUERY = 'SELECT * from users WHERE accessToken = :accessToken';

    private const CREATE_USER_QUERY = <<<SQL
        INSERT INTO users
        (email, name, password, gender, ip, accessToken, phone, dob, createdAt)
        values (:email, :name, :password, :gender, :ip, :accessToken, :phone, :dob, :createdAt)
SQL;

    private const UPDATE_USER_QUERY = <<<SQL
        UPDATE users SET
        email = :email, name = :name, password = :password, dob = :dob, gender = :gender, phone = :phone
        WHERE id = :id
SQL;

    private const UPDATE_ACCESS_TOKEN_QUERY = 'UPDATE users SET accessToken = :accessToken WHERE id = :id';

    private const DELETE_BY_NAME_QUERY = 'DELETE FROM users WHERE id = :id';

    private const FILTER_QUERY = "SELECT id, name, gender, round((julianday('now') - julianday(dob))/365,1) as age FROM users";

    private const HAS_EMAIL_QUERY = 'SELECT id as count FROM users WHERE email = :email AND id != :id';

    private const HAS_NAME_QUERY = 'SELECT id as count FROM users WHERE name = :name AND id != :id';

    /**
     * @var \PDO
     */
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function find(int $id): UserModel
    {
        $statement = $this->pdo->prepare(static::FIND_BY_ID_QUERY);
        $statement->bindParam(':id', $id, PDO::PARAM_INT);
        $statement->execute();

        $user = $statement->fetchObject(UserModel::class);

        if ($user === false) {
            throw new EntityNotFoundException("Can not find user with id: {$id}");
        }

        return $user;
    }

    public function findByEmail(string $email): UserModel
    {
        $statement = $this->pdo->prepare(static::FIND_BY_EMAIL_QUERY);
        $statement->bindParam(':email', $email, PDO::PARAM_STR);
        $statement->execute();

        $user = $statement->fetchObject(UserModel::class);

        if ($user === false) {
            throw new EntityNotFoundException("Can not find user with email: {$email}");
        }

        return $user;
    }

    public function hasByEmail(string $email): bool
    {
        try {
            $this->findByEmail($email);

            return true;
        } catch (EntityNotFoundException $exception) {
            return false;
        }
    }

    public function findByAccessToken(string $token): UserModel
    {
        $statement = $this->pdo->prepare(static::FIND_BY_ACCESS_TOKEN_QUERY);
        $statement->bindParam(':accessToken', $token, PDO::PARAM_STR);
        $statement->execute();

        $user = $statement->fetchObject(UserModel::class);

        if ($user === false || JWT::decode($token)['email'] !== $user->getEmail()) {
            throw new EntityNotFoundException('Can not find user with given token!');
        }

        return $user;
    }

    public function create(UserModel $user): UserModel
    {
        $user->beforeSave();

        $statement = $this->pdo->prepare(static::CREATE_USER_QUERY);

        $statement->execute(
            [
                ':email' => $user->getEmail(),
                ':name' => $user->getName(),
                ':password' => $user->getPassword(),
                ':gender' => $user->getGender(),
                ':dob' => $user->getDob(),
                ':ip' => $user->getIp(),
                ':accessToken' => $user->getAccessToken(),
                ':phone' => $user->getPhone(),
                ':createdAt' => $user->getCreatedAt()
            ]
        );

        return $user->setId((int) $this->pdo->lastInsertId());
    }

    public function update(UserModel $user): bool
    {
        $statement = $this->pdo->prepare(static::UPDATE_USER_QUERY);
        $id = $user->getId();
        $statement->bindParam(':id', $id, PDO::PARAM_INT);

        return $statement->execute(
            [
                ':name' => $user->getName(),
                ':email' => $user->getEmail(),
                ':password' => $user->getPassword(),
                ':gender' => $user->getGender(),
                ':dob' => $user->getDob(),
                ':phone' => $user->getPhone(),
                ':id' => $user->getId()
            ]
        );
    }

    public function updateAccessToken(int $id, string $token): bool
    {
        $statement = $this->pdo->prepare(static::UPDATE_ACCESS_TOKEN_QUERY);
        $statement->bindParam(':accessToken', $token, PDO::PARAM_STR);
        $statement->bindParam(':id', $id, PDO::PARAM_INT);

        return $statement->execute();
    }

    public function delete(int $id): bool
    {
        $statement = $this->pdo->prepare(static::DELETE_BY_NAME_QUERY);

        $statement->bindParam(':id', $id, PDO::PARAM_INT);

        return $statement->execute();
    }

    public function filter(int $gender = null, int $ageMin = null, int $ageMax = null): array
    {
        $statement = $this->pdo->prepare($this->createFilterQuery($gender, $ageMin, $ageMax));

        $this->bindFilterParameters($statement, $gender, $ageMin, $ageMax);

        $statement->execute();

        return $statement->fetchAll(
            PDO::FETCH_FUNC,
            function ($id, $name, $gender, $age) {
                return [
                    'id' => (int) $id,
                    'name' => $name,
                    'gender' => (int) $gender,
                    'age' => (int) $age
                ];
            }
        );
    }

    public function hasEmail(string $email, int $currentUserId = 0): bool
    {
        $statement = $this->pdo->prepare(static::HAS_EMAIL_QUERY);
        $statement->bindParam(':email', $email, PDO::PARAM_STR);
        $statement->bindParam(':id', $currentUserId, PDO::PARAM_INT);

        $statement->execute();
        $count = $statement->fetchColumn(0);

        return (int) $count > 0;
    }

    public function hasName(string $name, int $currentUserId = 0): bool
    {
        $statement = $this->pdo->prepare(static::HAS_NAME_QUERY);
        $statement->bindParam(':name', $name, PDO::PARAM_STR);
        $statement->bindParam(':id', $currentUserId, PDO::PARAM_INT);

        $statement->execute();
        $count = $statement->fetchColumn(0);

        return (int) $count > 0;
    }

    private function createFilterQuery(int $gender = null, int $ageMin = null, int $ageMax = null): string
    {
        $wheres = [];

        if ($gender !== null) {
            $wheres[] = 'gender = :gender';
        }

        if ($ageMin !== null) {
            $wheres[] = 'age >= :ageMin';
        }

        if ($ageMax !== null) {
            $wheres[] = 'age <= :ageMax';
        }

        if (\count($wheres) === 0) {
            return static::FILTER_QUERY;
        }

        return static::FILTER_QUERY . ' WHERE ' . \implode(' AND ', $wheres);
    }

    private function bindFilterParameters(
        PDOStatement $statement,
        int $gender = null,
        int $ageMin = null,
        int $ageMax = null
    ): void {
        if ($gender !== null) {
            $statement->bindParam(':gender', $gender, PDO::PARAM_INT);
        }

        if ($ageMin !== null) {
            $statement->bindParam(':ageMin', $ageMin, PDO::PARAM_INT);
        }

        if ($ageMax !== null) {
            $statement->bindParam(':ageMax', $ageMax, PDO::PARAM_INT);
        }
    }
}
