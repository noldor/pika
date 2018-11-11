<?php

declare(strict_types=1);

namespace Tests;

use App\Models\User;
use App\Repositories\User as UserRepository;
use DateTime;
use PDO;
use PHPUnit\DbUnit\Database\DefaultConnection;
use PHPUnit\DbUnit\DataSet\YamlDataSet;
use PHPUnit\DbUnit\TestCaseTrait;

abstract class DatabaseTestCase extends TestCase
{
    use TestCaseTrait {
        setUp as traitSetUp;
    }

    private $connection;

    /**
     * @var \App\Repositories\User
     */
    protected $userRepository;

    /**
     * @var \PDO
     */
    protected $pdo;

    protected function setUp()
    {
        parent::setUp();
        $this->traitSetUp();
        $this->userRepository = new UserRepository($this->pdo);
    }

    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();
        \unlink(\DB_PATH);
    }

    public function getConnection(): DefaultConnection
    {
        if ($this->connection === null) {
            $this->pdo = new PDO(PDO_DSN);
            $this->pdo->exec($this->getDatabaseDefinitionSql());
            $this->connection = $this->createDefaultDBConnection($this->pdo);
        }

        return $this->connection;
    }

    protected function getDataSet(): YamlDataSet
    {
        return new YamlDataSet(stubPath('/db_stub.yml'));
    }

    protected function createUser(
        string $email,
        string $name,
        string $accessToken,
        string $password = '123456',
        int $gender = 1,
        string $dob = null,
        string $phone = null
    ): User {
        $user = User::create(
            $email,
            $name,
            $password,
            $gender,
            $dob ?? (new DateTime())->format(DateTime::ATOM),
            '127.0.0.1',
            $accessToken,
            $phone
        );

        (new UserRepository($this->pdo))->create($user);

        return $user;
    }

    private function getDatabaseDefinitionSql(): string
    {
        return <<<SQL
            create table users
            (
              id INTEGER
                constraint table_name_pk
                  primary key,
              email TEXT not null,
              name TEXT not null,
              password TEXT not null,
              gender INTEGER default 0,
              dob TEXT not null,
              createdAt TEXT not null,
              accessToken TEXT not null,
              ip TEXT not null,
              phone TEXT
            );
            
            create index table_name_dob_index
              on users (dob);
            
            create unique index table_name_email_uindex
              on users (email);
            
            create unique index table_name_name_uindex
              on users (name);
SQL;
    }
}
