<?php

declare(strict_types=1);

namespace App\Controller\Boundaries;

use App\Controller\Boundaries\Common\CSRFinput;
use App\Domain\Roles;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Mapping\ClassMetadata;

class RegistrationInput
{
    // Symfony Forms handle the CSRF token field in the data input automatically
    // but if it wouldn't the CSRFinput shows example of how we'd implement such common field using a PHP Trait :-)
    // use CSRFinput;

    // I personally don't like this setter/getter approach (and symfony Forms approach in general) for write-once DTO's
    // I'd do something like following:
    // public function __construct(
    //     public readonly string $email,
    //     public readonly SomeRoleEnum $role,
    //     public readonly string $password,
    //     public readonly string $name,
    // ) {
    // }
    //...
    // ... But to present common getter/setter way

    public const FIELD_EMAIL = 'email';
    public const FIELD_ROLE = 'role';
    public const FIELD_PASSWORD = 'password';
    public const FIELD_NAME = 'name';

    private string $email;
    private Roles $role;
    private string $password;
    private string $name;

    public static function loadValidatorMetadata(ClassMetadata $metadata): void
    {
        $metadata->addPropertyConstraint(self::FIELD_EMAIL, new NotBlank());
        $metadata->addPropertyConstraint(self::FIELD_EMAIL, new Email());
        $metadata->addPropertyConstraint(self::FIELD_ROLE, new NotBlank());
        $metadata->addPropertyConstraint(self::FIELD_PASSWORD, new NotBlank());
        $metadata->addPropertyConstraint(self::FIELD_PASSWORD, new Length([
            'min' => 8,
            'max' => 50,
            'minMessage' => 'minimal password length is {{ limit }} characters',
            'maxMessage' => 'maximal password length is {{ limit }} characters',
        ]));
        $metadata->addPropertyConstraint(self::FIELD_NAME, new NotBlank());
        $metadata->addPropertyConstraint(self::FIELD_NAME, new Length([
            // No min => friendly for the whole world
            'max' => 200,
            'maxMessage' => 'maximum name length is {{ limit }} characters,' .
                ' if you actually have a longer name, please contact the administrator',
        ]));
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getRole(): Roles
    {
        return $this->role;
    }

    public function setRole(Roles $role): void
    {
        $this->role = $role;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }
}
