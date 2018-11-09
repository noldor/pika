<?php

declare(strict_types=1);

namespace Tests;

use PDO;
use PHPUnit\DbUnit\Database\DefaultConnection;
use PHPUnit\DbUnit\TestCaseTrait;

abstract class DatabaseTestCase extends TestCase
{
    use TestCaseTrait;

    private $connection;

    protected static $pdo;

    public function getConnection(): DefaultConnection
    {
        if ($this->connection === null) {
            if (static::$pdo === null) {
                static::$pdo = new PDO(PDO_DSN);
                static::$pdo->exec($this->getDatabaseDefinitionSql());
            }
            $this->connection = $this->createDefaultDBConnection(static::$pdo, ':memory:');
        }

        return $this->connection;
    }

    protected function getDatabaseDefinitionSql()
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
              ip INTEGER not null,
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
