<?php

declare(strict_types=1);

namespace Tests\App\Repositories;

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

    protected function getDataSet(): YamlDataSet
    {
        return new YamlDataSet(__DIR__ . '/dataset.yml');
    }

    public function testRepositoryCanFindUserById(): void
    {
        $this->assertArraySubset([
            'id' => '1',
            'name' => 'first',
            'password' => '123456',
            'email' => 'first@test.ru',
            'dob' => '2018-11-09T16:22:21+00:00',
            'gender' => '1',
            'phone' => null,
            'createdAt' => '2018-11-09T16:22:21+00:00',
            'ip' => '127.0.0.1'
        ], $this->repository->find(1)->toArray());
    }

    public function testFindThrowExceptionWhenEntityDoesNotFound(): void
    {
        $this->expectException(EntityNotFoundException::class);
        $this->expectExceptionMessage('Can not find user with id: 1000');

        $this->repository->find(1000);
    }

    public function testRepositoryCanFindUserByEmail(): void
    {
        $this->assertArraySubset([
            'id' => '1',
            'name' => 'first',
            'password' => '123456',
            'email' => 'first@test.ru',
            'dob' => '2018-11-09T16:22:21+00:00',
            'gender' => '1',
            'phone' => null,
            'createdAt' => '2018-11-09T16:22:21+00:00',
            'ip' => '127.0.0.1'
        ], $this->repository->findByEmail('first@test.ru')->toArray());
    }

    public function testFindByEmailThrowExceptionWhenEntityDoesNotFound(): void
    {
        $this->expectException(EntityNotFoundException::class);
        $this->expectExceptionMessage('Can not find user with email: not@test.ru');

        $this->repository->findByEmail('not@test.ru');
    }

    public function testRepositoryCanFindUserByAccessToken(): void
    {
        $token = 'eyJhbGciOiJzaGE1MTIiLCJ0eXAiOiJKV1QiLCJraWQiOjU1OTQ3NjU0NjQ0NTc0Nzg2NDN9.eyJlbWFpbCI6ImZpcnN0QHRlc3QucnUifQ==.nddz97FBeb96vIhg0DUaUJhCCRr9hatlQgZUKNioz2Ki01xD6aqNPf8L7enVPZmMYNemXxTx+5jo5U6uUfevwA==';
        $this->assertSame([
            'id' => '1',
            'name' => 'first',
            'password' => '123456',
            'email' => 'first@test.ru',
            'dob' => '2018-11-09T16:22:21+00:00',
            'gender' => '1',
            'phone' => null,
            'createdAt' => '2018-11-09T16:22:21+00:00',
            'ip' => '127.0.0.1',
            'accessToken' => $token,
        ], $this->repository->findByAccessToken($token)->toArray());
    }

    public function testFindByAccessTokenThrowExceptionWhenEntityDoesNotFound(): void
    {
        $this->expectException(EntityNotFoundException::class);
        $this->expectExceptionMessage('Can not find user with email: some@test.ru');

        $token = JWT::encode(['email' => 'some@test.ru'], \APP_SECRET_KEY);

        $this->repository->findByAccessToken($token);
    }

    public function testRepositoryCanSaveNewUser(): void
    {
        $dob = (new DateTime())->format(DateTime::ATOM);
        $token = JWT::encode(['email' => 'third@test.ru'], \APP_SECRET_KEY);
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

        $dbUser = $this->repository->find(15);

        $this->assertArraySubset([
            'email' => 'third@test.ru',
            'name' => 'third',
            'gender' => '2',
            'dob' => $dob,
            'accessToken' => $token,
            'phone' => '+79106119988',
            'ip' => '127.0.0.1',
        ], $dbUser->toArray());
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

        $this->assertArraySubset([
            'name' => '2-second',
            'email' => '1-second@test.ru',
            'dob' => $dob,
            'gender' => 0,
            'phone' => '+79997776655'
        ], $user->toArray());
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
        $this->assertCount(4, $this->repository->filter(1));
        $this->assertSame([
            [
                'id' => 1,
                'name' => 'first',
                'gender' => 1,
                'age' => 0
            ],
            [
                'id' => 2,
                'name' => 'second',
                'gender' => 1,
                'age' => 0
            ],
            [
                'id' => 11,
                'name' => '11-name',
                'gender' => 1,
                'age' => 2
            ],
            [
                'id' => 14,
                'name' => '14-name',
                'gender' => 1,
                'age' => 5
            ]
        ], $this->repository->filter(1));
    }

    public function testFilterByMinAge(): void
    {
        $this->assertCount(2, $this->repository->filter(null, 4));
        $this->assertSame([
            [
                'id' => 13,
                'name' => '13-name',
                'gender' => 0,
                'age' => 4
            ],
            [
                'id' => 14,
                'name' => '14-name',
                'gender' => 1,
                'age' => 5
            ]
        ], $this->repository->filter(null, 4));
    }

    public function testFilterByGenderMinAge(): void
    {
        $this->assertCount(1, $this->repository->filter(1, 4));
        $this->assertSame([
            [
                'id' => 14,
                'name' => '14-name',
                'gender' => 1,
                'age' => 5
            ]
        ], $this->repository->filter(1, 4));
    }

    public function testFilterByMaxAge(): void
    {
        $this->assertCount(3, $this->repository->filter(null, null, 1));
        $this->assertSame([
            [
                'id' => 1,
                'name' => 'first',
                'gender' => 1,
                'age' => 0
            ],
            [
                'id' => 2,
                'name' => 'second',
                'gender' => 1,
                'age' => 0
            ],
            [
                'id' => 10,
                'name' => '10-name',
                'gender' => 0,
                'age' => 1
            ]
        ], $this->repository->filter(null, null, 1));
    }

    public function testFilterWithoutAnyArguments()
    {
        $this->assertCount(7, $this->repository->filter());
    }
}
