<?php

declare(strict_types=1);

namespace Tests\Unit\App\Controllers\User;

use App\Controllers\User\Filter;
use Support\Request\Request;
use Tests\DatabaseTestCase;

class FilterTest extends DatabaseTestCase
{
    public function testHandleCanFilterUsersByGender(): void
    {
        $response = (new Filter(
            Request::create(['gender' => 1]),
            $this->userRepository
        ))->handle();

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
            $response->getData()
        );
    }

    public function testHandleCanFilterUsersByGenderAndMinAge(): void
    {
        $response = (new Filter(
            Request::create(['gender' => 1, 'age_min' => 4]),
            $this->userRepository
        ))->handle();

        $this->assertSame(
            [
                [
                    'id' => 7,
                    'name' => '7-name',
                    'gender' => 1,
                    'age' => 5
                ]
            ],
            $response->getData()
        );
    }

    public function testHandleCanFilterUsersByGenderAndMinAgeAndMaxAge(): void
    {
        $response = (new Filter(
            Request::create(['gender' => 1, 'age_min' => 1, 'age_max' => 5]),
            $this->userRepository
        ))->handle();

        $this->assertSame(
            [
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
            $response->getData()
        );
    }

    public function testHandleReturnAllUsersWithoutFilters(): void
    {
        $response = (new Filter(
            Request::create([]),
            $this->userRepository
        ))->handle();

        $this->assertCount(7, $response->getData());
    }
}
