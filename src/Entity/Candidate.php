<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class Candidate
{
    private SocialMedia $socialMedia;

    public function __construct()
    {
        $this->socialMedia = new SocialMedia();
    }

    protected ?int $id = null;

    #[Assert\NotBlank(message: "Firstname is required")]
    #[Assert\Length(
        min: 3,
        max: 255,
        minMessage: "Firstname must be at least {{ limit }} characters long",
        maxMessage: "Firstname must be at least {{ limit }} characters long"
    )]
    protected ?string $firstname = null;

    #[Assert\NotBlank(message: "Lastname is required")]
    #[Assert\Length(
        min: 3,
        max: 255,
        minMessage: "Lastname must be at least {{ limit }} characters long",
        maxMessage: "Lastname must be at least {{ limit }} characters long"
    )]
    protected ?string $lastname = null;

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

    #[Assert\Length(
        min: 3,
        max: 255,
        minMessage: "Address must be at least {{ limit }} characters long",
        maxMessage: "Address must be at least {{ limit }} characters long"
    )]
    protected ?string $address = null;

    #[Assert\Length(
        min: 3,
        max: 255,
        minMessage: "City must be at least {{ limit }} characters long",
        maxMessage: "City must be at least {{ limit }} characters long"
    )]
    protected ?string $city = null;

    #[Assert\Length(
        min: 3,
        max: 255,
        minMessage: "Country must be at least {{ limit }} characters long",
        maxMessage: "Country must be at least {{ limit }} characters long"
    )]
    protected ?string $country = null;

    protected ?string $avatar = null;

    protected  ?string $slug = null;

    protected ?string $coverLetter = null;

    #[Assert\NotBlank(message: "User id is required")]
    #[Assert\Type(type: 'integer', message: "User id must be an integer")]
    protected ?int $user_id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }


    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): void
    {
        $this->firstname = $firstname;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): void
    {
        $this->lastname = $lastname;
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

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): void
    {
        $this->avatar = $avatar;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $firstname, ?string $lastname): void
    {
        $this->slug = str_ireplace(' ', '-', $firstname . '-' . $lastname);
    }


    public function getCoverLetter(): ?string
    {
        return $this->coverLetter;
    }

    public function setCoverLetter(?string $coverLetter): void
    {
        $this->coverLetter = $coverLetter;
    }

    public function getSocialMedia(): SocialMedia
    {
        return $this->socialMedia;
    }

    public function getUser_id(): ?int
    {
        return $this->user_id;
    }

    public function setUser_id(?int $user_id): void
    {
        $this->user_id = $user_id;
    }
}