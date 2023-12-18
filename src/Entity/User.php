<?php

namespace App\Entity;

use DateTime;
use Symfony\Component\Validator\Constraints as Assert;

class User
{
    public const ROLES = [
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
    #[Assert\Unique(message: "An account already exists with this email")]
    protected ?string $email = null;

    #[Assert\NotBlank(message: "Password is required")]
    #[Assert\Regex(
        pattern: '/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/',
        message: 'Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial ',
        match: true)]
    protected ?string $password = null;

    protected ?int $roles = null;

    protected bool $is_verified = false;

    protected ?DateTime $created_at = null;

    protected ?DateTime $updated_at = null;

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
    public function getRoles(): ?array
    {
        $roles = [];
        foreach (self::ROLES as $role => $value) {
            if (($this->hasRole($role) & $value) === $value) {
                $roles[] = $role;
            }
        }
        return $roles;
    }

    public function getRolesInt(): ?int
    {
        return $this->roles;
    }
    public function setRoles(?array $roles): void
    {
        $this->roles = $roles;
    }

    public function isIsVerified(): bool
    {
        return $this->is_verified;
    }

    public function setIsVerified(bool $is_verified): void
    {
        $this->is_verified = $is_verified;
    }

    public function getCreatedAt(): ?DateTime
    {
        return $this->created_at;
    }

    public function setCreatedAt(?DateTime $created_at): void
    {
        $this->created_at = $created_at;
    }

    public function getUpdatedAt(): ?DateTime
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?DateTime $updated_at): void
    {
        $this->updated_at = $updated_at;
    }
}