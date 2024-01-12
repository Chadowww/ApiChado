<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class Company extends User
{
    private SocialMedia $socialMedia;

    public function __construct()
    {
        $this->socialMedia = new SocialMedia();
    }

    protected ?int $id = null;

    #[Assert\NotBlank(message: "Name is required")]
    #[Assert\Length(
        min: 3,
        max: 50,
        minMessage: "Name must be at least {{ limit }} characters long",
        maxMessage: "Name must be at least {{ limit }} characters long"
    )]
    protected ?string $name = null;

    #[Assert\NotBlank(message: "Phone is required")]
    #[Assert\Length(
        min: 10,
        max: 10,
        minMessage: "Phone must be at least {{ limit }} characters long",
        maxMessage: "Phone must be at least {{ limit }} characters long"
    )]
    #[Assert\Regex(
        pattern: '/^0[1-9]([-. ]?[0-9]{2}){4}$/',
        message: "Phone must be a valid phone number"
    )]
    protected ?string $phone = null;

    #[Assert\NotBlank(message: "Address is required")]
    #[Assert\Length(
        min: 3,
        max: 255,
        minMessage: "Address must be at least {{ limit }} characters long",
        maxMessage: "Address must be at least {{ limit }} characters long"
    )]
    protected ?string $address = null;

    #[Assert\NotBlank(message: "City is required")]
    #[Assert\Length(
        min: 3,
        max: 255,
        minMessage: "City must be at least {{ limit }} characters long",
        maxMessage: "City must be at least {{ limit }} characters long"
    )]
    protected ?string $city = null;

    #[Assert\NotBlank(message: "Country is required")]
    #[Assert\Length(
        min: 3,
        max: 255,
        minMessage: "Country must be at least {{ limit }} characters long",
        maxMessage: "Country must be at least {{ limit }} characters long"
    )]
    protected ?string $country = null;

    protected ?string $description = null;

    #[Assert\Length(
        min: 14,
        max: 14,
        minMessage: "Siret must be at least {{ limit }} characters long",
        maxMessage: "Siret must be at least {{ limit }} characters long"
    )]
    #[Assert\Regex(
        pattern: '/^[0-9]{14}$/',
        message: "Siret must be a valid Siret number"
    )]
    #[Assert\NotBlank(message: "Siret is required")]
    protected ?string $siret = null;

    protected ?string $logo = null;

    protected ?string $slug = null;

    protected ?string $cover = null;

    #[Assert\NotBlank(message: "User id is required")]
    #[Assert\Positive(message: "User id must be a positive number")]
    #[Assert\Type("integer", message: "User id must be a number")]
    protected ?int $userId = null;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): void
    {
        $this->phone = $phone;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): void
    {
        $this->address = $address;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): void
    {
        $this->city = $city;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): void
    {
        $this->country = $country;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getSiret(): ?string
    {
        return $this->siret;
    }

    public function setSiret(?string $siret): void
    {
        $this->siret = $siret;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(?string $logo): void
    {
        $this->logo = $logo;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $name): void
    {
        $this->slug = str_ireplace(' ', '-', $name);
    }

    public function getCover(): ?string
    {
        return $this->cover;
    }

    public function setCover(?string $cover): void
    {
        $this->cover = $cover;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(?int $userId): void
    {
        $this->userId = $userId;
    }

    public function getSocialMedia(): SocialMedia
    {
        return $this->socialMedia;
    }
}