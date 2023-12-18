<?php

namespace App\Entity;

use DateTime;
use Symfony\Component\Validator\Constraints as Assert;

class User
{
    private ?int $id = null;

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
    private ?string $email = null;

    #[Assert\NotBlank(message: "Password is required")]
    #[Assert\Regex(
        pattern: '/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/',
        message: 'Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial ',
        match: true)]
    private ?string $password = null;

    private ?array $roles = [];

    private bool $is_verified = false;

    private ?DateTime $created_at = null;
    private ?DateTime $updated_at = null;
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

    public function getRoles(): ?array
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
}