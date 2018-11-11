<?php

declare(strict_types=1);

namespace Tests\Unit\App\Validators;

use App\Repositories\User as UserRepository;
use App\Validators\User;
use DateTime;
use Support\Exceptions\ValidationException;
use Tests\DatabaseTestCase;

class UserTest extends DatabaseTestCase
{
    /**
     * @var User
     */
    private $validator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = new User(new UserRepository($this->pdo));
    }

    public function wrongEmailDataProvider(): array
    {
        return [
            [''],
            ['test'],
            ['test@test.ru ']
        ];
    }

    /**
     * @dataProvider wrongEmailDataProvider
     */
    public function testIsValidEmail(string $email): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Seem`s user email is not an email!');

        $this->validator->isValidEmail($email);
    }

    public function testIsValidEmailThrowExceptionOnDuplicateEmail(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('User with this email already exists!');

        $this->validator->isValidEmail('1-mail@test.ru');
    }

    public function testIsValidEmailReturnTrueOnValidEmail(): void
    {
        $this->assertTrue($this->validator->isValidEmail('test@test.ru'));
    }

    public function wrongNamesProvider(): array
    {
        return [
            [''],
            ['name+'],
            ['some name']
        ];
    }

    /**
     * @dataProvider wrongNamesProvider
     */
    public function testIsValidNameThrowExceptionOnNotValidName(string $name): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('User name must contain only latin or russian characters, digits and . and -');

        $this->validator->isValidName($name);
    }

    public function testIsValidEmailThrowExceptionOnDuplicateName(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('User with this name already exists!');

        $this->validator->isValidName('1-name');
    }

    public function testIsValidEmailReturnTrueOnValidName(): void
    {
        $this->assertTrue($this->validator->isValidName('name'));
    }

    public function wrongPasswords(): array
    {
        return [
            [''],
            ['1'],
            ['12'],
            ['123'],
            ['1234'],
            ['12345'],
            [\str_repeat('1', 256)],
            [\str_repeat('1', 257)]
        ];
    }

    /**
     * @dataProvider wrongPasswords
     */
    public function testIsValidPasswordWhenPasswordLessThenSixOrGreaterThan255(string $password): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage(
            'User password must have length greater or equal to 6 chars and less or equal to 255 chars!'
        );

        $this->validator->isValidPassword($password);
    }

    public function testThatIsValidPasswordReturnTrueOnRightPassword(): void
    {
        $this->assertTrue($this->validator->isValidPassword('123456789'));
    }

    public function phones(): array
    {
        return [
            ['+180074935247'],
            ['+5491187654321'],
            ['+4930123456'],
            ['+5212345678900'],
            ['+165025300001'],
            ['+800123456789'],
            ['+79106523456']
        ];
    }

    /**
     * @dataProvider phones
     */
    public function testIsValidPhone(string $phone): void
    {
        $this->assertTrue($this->validator->isValidPhone($phone));
    }

    public function wrongPhones(): array
    {
        return [
            [''],
            ['sss'],
            ['123456'],
            ['99106523456']
        ];
    }

    /**
     * @dataProvider wrongPhones
     */
    public function testIsValidPhoneThrowExceptionOnWrongPhone(string $phone): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Seem`s that phone not in international phone number format!');

        $this->validator->isValidPhone($phone);
    }

    public function testIsValidGender(): void
    {
        $this->assertTrue($this->validator->isValidGender(0));
        $this->assertTrue($this->validator->isValidGender(2));
        $this->assertTrue($this->validator->isValidGender(2));
    }

    public function wrongGenders(): array
    {
        return [
            [-2],
            [-1],
            [3],
            [4]
        ];
    }

    /**
     * @dataProvider wrongGenders
     */
    public function testIsValidGenderThrowExceptionOnWrongGender(int $gender): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('User gender must be one of [0, 1, 2]');

        $this->validator->isValidGender($gender);
    }

    public function testIsValidDateOfBirth(): void
    {
        $this->assertTrue($this->validator->isValidDateOfBirth('2018-11-09T06:25:08+00:00'));
    }

    public function datesOfBirth(): array
    {
        return [
            [(new DateTime())->format(DateTime::ISO8601)],
            [(new DateTime())->format(DateTime::COOKIE)],
            [(new DateTime())->format(DateTime::RFC822)],
            [(new DateTime())->format(DateTime::RFC850)],
            [(new DateTime())->format(DateTime::RFC1036)],
            [(new DateTime())->format(DateTime::RFC1123)],
            [(new DateTime())->format(DateTime::RFC2822)],
            [(new DateTime())->format(DateTime::RFC3339_EXTENDED)],
            [(new DateTime())->format(DateTime::RFC7231)],
            [(new DateTime())->format(DateTime::RSS)]
        ];
    }

    /**
     * @dataProvider datesOfBirth
     */
    public function testThatIsValidDateOfBirthThrowExceptionOnWrongDateFormat(string $dob): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Seem`s that date of birth has wrong format!');

        $this->validator->isValidDateOfBirth($dob);
    }
}
