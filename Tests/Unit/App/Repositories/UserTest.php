<?php

declare(strict_types=1);

namespace Tests\Unit\App\Repositories;

use App\Repositories\User;
use DateTime;
use PHPUnit\DbUnit\DataSet\YamlDataSet;
use Support\Exceptions\EntityNotFoundException;
use Support\JWT;
use Tests\DatabaseTestCase;

class UserTest extends DatabaseTestCase
{
    /**
     * @var User
     */
    private $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new User(static::$pdo);
    }

    public function testRepositoryCanFindUserById(): void
    {
        $this->assertArraySubset(
            [
                'id' => 1,
                'name' => '1-name',
                'password' => '123456',
                'email' => '1-mail@test.ru',
                'dob' => '2018-11-09T16:22:21+00:00',
                'gender' => 1,
                'phone' => null,
                'createdAt' => '2018-11-09T16:22:21+00:00',
                'ip' => '127.0.0.1'
            ],
            $this->repository->find(1)->toArray()
        );
    }

    public function testFindThrowExceptionWhenEntityDoesNotFound(): void
    {
        $this->expectException(EntityNotFoundException::class);
        $this->expectExceptionMessage('Can not find user with id: 1000');

        $this->repository->find(1000);
    }

    public function testRepositoryCanFindUserByEmail(): void
    {
        $this->assertArraySubset(
            [
                'id' => 1,
                'name' => '1-name',
                'password' => '123456',
                'email' => '1-mail@test.ru',
                'dob' => '2018-11-09T16:22:21+00:00',
                'gender' => 1,
                'phone' => null,
                'createdAt' => '2018-11-09T16:22:21+00:00',
                'ip' => '127.0.0.1'
            ],
            $this->repository->findByEmail('1-mail@test.ru')->toArray()
        );
    }

    public function testFindByEmailThrowExceptionWhenEntityDoesNotFound(): void
    {
        $this->expectException(EntityNotFoundException::class);
        $this->expectExceptionMessage('Can not find user with email: not@test.ru');

        $this->repository->findByEmail('not@test.ru');
    }

    public function testHasByEmailReturnTrueWhenUserExist(): void
    {
        $this->createUser('test@test.ru', 'name', JWT::encode(['email' => 'test@test.ru']));

        $this->assertTrue($this->repository->hasByEmail('test@test.ru'));
    }

    public function testHasByEmailReturnFalseWhenUserDoesNotExist(): void
    {
        $this->assertFalse($this->repository->hasByEmail('test@test.ru'));
    }

    public function testRepositoryCanFindUserByAccessToken(): void
    {
        $token = 'eyJhbGciOiJzaGE1MTIiLCJ0eXAiOiJKV1QiLCJraWQiOi0yMjQ0NDc5NjUwNzkzNDQ1NDgxfQ==.eyJlbWFpbCI6IjEtbWFpbEB0ZXN0LnJ1In0=.YrYjvuFdLdgI2WXTN+Plu7MN/bZUTOQbLI3FqR4pnHpgBTEchFdXUHYsuJeyIg+HKPjJuAqyT6ZDqNb/dChf0w==';
        $this->assertSame(
            [
                'id' => 1,
                'name' => '1-name',
                'password' => '123456',
                'email' => '1-mail@test.ru',
                'dob' => '2018-11-09T16:22:21+00:00',
                'gender' => 1,
                'phone' => null,
                'createdAt' => '2018-11-09T16:22:21+00:00',
                'ip' => '127.0.0.1',
                'accessToken' => $token,
            ],
            $this->repository->findByAccessToken($token)->toArray()
        );
    }

    public function testFindByAccessTokenThrowExceptionWhenEntityDoesNotFound(): void
    {
        $this->expectException(EntityNotFoundException::class);
        $this->expectExceptionMessage('Can not find user with given token!');

        $token = JWT::encode(['email' => 'some@test.ru']);

        $this->repository->findByAccessToken($token);
    }

    public function testRepositoryCanSaveNewUser(): void
    {
        $dob = (new DateTime())->format(DateTime::ATOM);
        $token = JWT::encode(['email' => 'third@test.ru']);
        $user = \App\Models\User::create(
            'third@test.ru',
            'third',
            '123456',
            2,
            $dob,
            '127.0.0.1',
            $token,
            '+79106119988'
        );

        $this->repository->create($user);

        $dbUser = $this->repository->find(8);

        $this->assertArraySubset(
            [
                'email' => 'third@test.ru',
                'name' => 'third',
                'gender' => 2,
                'dob' => $dob,
                'accessToken' => $token,
                'phone' => '+79106119988',
                'ip' => '127.0.0.1',
            ],
            $dbUser->toArray()
        );
    }

    public function testRepositoryCanUpdateUser(): void
    {
        $oldUser = clone $this->repository->find(2);

        $user = clone $oldUser;
        $user->setEmail('1-second@test.ru');
        $user->setName('2-second');
        $user->setPassword('654321');
        $user->setGender(0);
        $dob = (new DateTime())->format(DateTime::ATOM);
        $user->setDob($dob);
        $user->setPhone('+79997776655');

        $this->repository->update($user);

        $user = $this->repository->find(2);

        $this->assertNotSame($oldUser->getEmail(), $user->getEmail());
        $this->assertNotSame($oldUser->getName(), $user->getName());
        $this->assertNotSame($oldUser->getPassword(), $user->getPassword());
        $this->assertNotSame($oldUser->getGender(), $user->getGender());
        $this->assertNotSame($oldUser->getDob(), $user->getDob());
        $this->assertNotSame($oldUser->getPhone(), $user->getPhone());

        $this->assertArraySubset(
            [
                'name' => '2-second',
                'email' => '1-second@test.ru',
                'dob' => $dob,
                'gender' => 0,
                'phone' => '+79997776655'
            ],
            $user->toArray()
        );
    }

    public function testUpdateAccessToken(): void
    {
        $this->repository->updateAccessToken(1, '000');

        $this->assertSame('000', $this->repository->find(1)->getAccessToken());
    }

    public function testDelete(): void
    {
        $user = $this->repository->find(2);

        $this->assertInstanceOf(\App\Models\User::class, $user);

        $this->repository->delete(2);

        $this->expectException(EntityNotFoundException::class);

        $this->repository->find(2);
    }

    public function testFilterByGender(): void
    {
        $this->assertSame(
            [
                [
                    'id' => 1,
                    'name' => '1-name',
                    'gender' => 1,
                    'age' => 0
                ],
                [
                    'id' => 2,
                    'name' => '2-name',
                    'gender' => 1,
                    'age' => 0
                ],
                [
                    'id' => 4,
                    'name' => '4-name',
                    'gender' => 1,
                    'age' => 2
                ],
                [
                    'id' => 7,
                    'name' => '7-name',
                    'gender' => 1,
                    'age' => 5
                ]
            ],
            $this->repository->filter(1)
        );
    }

    public function testFilterByMinAge(): void
    {
        $this->assertCount(2, $this->repository->filter(null, 4));
        $this->assertSame(
            [
                [
                    'id' => 6,
                    'name' => '6-name',
                    'gender' => 0,
                    'age' => 4
                ],
                [
                    'id' => 7,
                    'name' => '7-name',
                    'gender' => 1,
                    'age' => 5
                ]
            ],
            $this->repository->filter(null, 4)
        );
    }

    public function testFilterByGenderMinAge(): void
    {
        $this->assertCount(1, $this->repository->filter(1, 4));
        $this->assertSame(
            [
                [
                    'id' => 7,
                    'name' => '7-name',
                    'gender' => 1,
                    'age' => 5
                ]
            ],
            $this->repository->filter(1, 4)
        );
    }

    public function testFilterByMaxAge(): void
    {
        $this->assertCount(3, $this->repository->filter(null, null, 1));
        $this->assertSame(
            [
                [
                    'id' => 1,
                    'name' => '1-name',
                    'gender' => 1,
                    'age' => 0
                ],
                [
                    'id' => 2,
                    'name' => '2-name',
                    'gender' => 1,
                    'age' => 0
                ],
                [
                    'id' => 3,
                    'name' => '3-name',
                    'gender' => 0,
                    'age' => 1
                ]
            ],
            $this->repository->filter(null, null, 1)
        );
    }

    public function testFilterWithoutAnyArguments(): void
    {
        $this->assertCount(7, $this->repository->filter());
    }
}
