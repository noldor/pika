<?php

declare(strict_types=1);

namespace App\Models;

use DateTime;

class User
{
    /**
     * @var int|null
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $dob;

    /**
     * @var int|string
     */
    private $gender;

    /**
     * @var string
     */
    private $phone;

    /**
     * @var string
     */
    private $createdAt;

    /**
     * @var string
     */
    private $ip;

    /**
     * @var string
     */
    private $accessToken;

    public static function create(
        string $email,
        string $name,
        string $password,
        int $gender,
        string $dob,
        string $ip,
        string $accessToken,
        string $phone = null
    ) {
        $user = new static();

        $user->setEmail($email);
        $user->setName($name);
        $user->setPassword($password);
        $user->setDob($dob);
        $user->setGender($gender);
        $user->setIp($ip);
        $user->setAccessToken($accessToken);
        $user->setPhone($phone);

        return $user;
    }

    public function beforeSave(): void
    {
        $this->createdAt = (new DateTime())->format(DateTime::ATOM);
    }

    public function getId(): int
    {
        return (int) $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = \password_hash($password, \PASSWORD_DEFAULT);

        return $this;
    }

    public function verifyPassword(string $password): bool
    {
        return \password_verify($password, $this->password);
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getDob(): string
    {
        return $this->dob;
    }

    public function setDob(string $dob): self
    {
        $this->dob = $dob;

        return $this;
    }

    public function getGender(): int
    {
        return (int) $this->gender;
    }

    public function setGender(int $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function setCreatedAt(string $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getIp(): string
    {
        return $this->ip;
    }

    public function setIp(string $ip): self
    {
        $this->ip = $ip;

        return $this;
    }

    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    public function setAccessToken(string $accessToken): self
    {
        $this->accessToken = $accessToken;

        return $this;
    }

    public function only(string ...$fields): array
    {
        return \array_reduce(
            $fields,
            function ($array, $key) {
                $array[$key] = $this->{$key};

                return $array;
            },
            []
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'password' => $this->password,
            'email' => $this->email,
            'dob' => $this->dob,
            'gender' => $this->gender,
            'phone' => $this->phone,
            'createdAt' => $this->createdAt,
            'ip' => $this->ip,
            'accessToken' => $this->accessToken
        ];
    }
}
