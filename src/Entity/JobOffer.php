<?php

namespace App\Entity;

use DateTime;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

class JobOffer
{
    /**
     * @SerializedName("jobofferId")
     */
    private ?int $jobofferId = null;

    /**
     * @SerializedName("title")
     */
    #[Assert\NotBlank(message: "Title is required")]
    #[Assert\Type("string", message: "Title must be a string")]
    #[Assert\Length(
        min: 3,
        max: 50,
        minMessage: "Title must be at least {{ limit }} characters long",
        maxMessage: "Title must be at least {{ limit }} characters long",
    )]
    private ?string $title = null;

    /**
     * @var string|null $description
     */
    #[Assert\NotBlank(message: "Description is required")]
    #[Assert\Type("string", message: "Description must be a string")]
    #[Assert\Length(
        min: 50,
        max: 2500,
        minMessage: "Description must be at least {{ limit }} characters long",
        maxMessage: "Description must be at least {{ limit }} characters long"
    )]
    private ?string $description = null;

    /**
     * @var string|null $city
     */
    #[Assert\NotBlank(message: "City is required")]
    #[Assert\Type("string", message: "City must be a string")]
    #[Assert\Length(
        min: 3,
        max: 255,
        minMessage: "City must be at least {{ limit }} characters long",
        maxMessage: "City must be at least {{ limit }} characters long"
    )]
    private ?string $city = null;

    /**
     * @var int|null $salaryMin
     */
    #[Assert\NotBlank(message: "Salary min is required")]
    #[Assert\Positive(message: "Salary min must be a positive number")]
    #[Assert\Type("integer", message: "Salary min must be a number")]
    private ?int $salaryMin = null;

    /**
     * @var int|null $salaryMax
     */
    #[Assert\NotBlank(message: "Salary min is required")]
    #[Assert\Positive(message: "Salary max must be a positive number")]
    #[Assert\Type("integer", message: "Salary max must be a number")]
    private ?int $salaryMax = null;

    private DateTime $created_at;
    private DateTime $updated_at;


    public function getJobofferId(): ?int
    {
        return $this->jobofferId;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): void
    {
        $this->city = $city;
    }

    public function getSalaryMin(): ?int
    {
        return $this->salaryMin;
    }

    public function setSalaryMin(?int $salaryMin): void
    {
        $this->salaryMin = $salaryMin;
    }

    public function getSalaryMax(): ?int
    {
        return $this->salaryMax;
    }

    public function setSalaryMax(?int $salaryMax): void
    {
        $this->salaryMax = $salaryMax;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->created_at;
    }

    public function setCreatedAt(DateTime $created_at): void
    {
        $this->created_at = $created_at;
    }

    public function getUpdatedAt(): DateTime
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(DateTime $updated_at): void
    {
        $this->updated_at = $updated_at;
    }


}