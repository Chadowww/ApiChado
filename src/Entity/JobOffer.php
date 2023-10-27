<?php

namespace App\Entity;

use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @SerializedName("jobOffer")
 */
class JobOffer
{
    /**
     * @SerializedName("id")
     */
    #[Assert\Positive]
    #[Assert\Type("integer")]
    #[Assert\Unique]
    private ?int $id = null;

    /**
     * @SerializedName("title")
     */
    #[Assert\NotBlank]
    #[Assert\Type("string")]
    #[Assert\Length(min: 3, max: 255)]
    private ?string $title = null;

    /**
     * @var string|null $description
     */
    #[Assert\NotBlank]
    #[Assert\Type("string")]
    #[Assert\Length(min: 3, max: 2500)]
    private ?string $description = null;

    /**
     * @var string|null $city
     */
    #[Assert\NotBlank]
    #[Assert\Type("string")]
    #[Assert\Length(min: 3, max: 255)]
    private ?string $city = null;

    /**
     * @var int|null $salaryMin
     */
    #[Assert\Positive]
    #[Assert\Type("integer")]
    private ?int $salaryMin = null;

    /**
     * @var int|null $salaryMax
     */
    #[Assert\Positive]
    #[Assert\Type("integer")]
    private ?int $salaryMax = null;


    public function getId(): ?int
    {
        return $this->id;
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
}