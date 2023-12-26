<?php

namespace App\Entity;

use DateTime;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

class User implements PasswordAuthenticatedUserInterface, UserInterface
{
    public const array ROLES = [
        'ROLE_USER' => 1,
        'ROLE_CANDIDATE' => 2,
        'ROLE_COMPANY' => 4,
        'ROLE_ADMIN' => 8,
    ];

    protected ?int $id = null;

    #[Assert\NotBlank(message: "Email is required")]
    #[Assert\Email(message: "Email must be a valid email")]
    #[Assert\Length(
        min: 3,
        max: 255,
        minMessage: "Email must be at least {{ limit }} characters long",
        maxMessage: "Email must be at least {{ limit }} characters long"
    )]
    #[Assert\Regex(
        pattern: '/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/',
        message: "Email must be a valid email"
    )]
    protected ?string $email = null;

    #[Assert\NotBlank(message: "Password is required")]
    #[Assert\Regex(
        pattern: '/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/',
        message: 'Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial ',
        match: true)]
    protected ?string $password = null;

    #[Assert\NotBlank(message: "Role is required")]
    #[Assert\Choice(
        choices: [1, 3, 5, 9],
        message: "Role must be one of the following: 1 (ROLE_USER), 3 (ROLE_CANDIDATE), 5 (ROLE_COMPANY), 9 (ROLE_ADMIN)"
    )]
    protected ?int $roles = null;

    protected bool $is_verified = false;

    protected string | DateTime | null $created_at = null;

    protected string | DateTime | null $updated_at = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): void
    {
        $this->password = $password;
    }

    public function hasRole(string $role): bool
    {
        return ($this->roles & self::ROLES[$role]) === self::ROLES[$role];
    }
    public function getRoles(): array
    {
        return $this->getArrayRoles();
    }

    public function getArrayRoles(): array
    {

        $roles = [];
        foreach (self::ROLES as $role => $value) {
            if (($this->roles & $value) === $value) {
                $roles[] = $role;
            }
        }
        return $roles;
    }

    public function setRoles(?int $roleValue): void
    {
        $this->roles = $roleValue;
    }

    public function isIsVerified(): bool
    {
        return $this->is_verified;
    }

    public function setIsVerified(bool $is_verified): void
    {
        $this->is_verified = $is_verified;
    }

    public function getCreatedAt(): DateTime | string
    {
        return $this->created_at;
    }

    public function setCreatedAt(?DateTime $created_at): void
    {
        $this->created_at = $created_at;
    }

    public function getUpdatedAt(): ?string
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?DateTime $updated_at): void
    {
        $this->updated_at = $updated_at;
    }

    #[\Override] public function eraseCredentials()
    {

    }

    #[\Override] public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }
}