<?php

namespace App\Entity;

use Symfony\Component\Serializer\Annotation\SerializedName;

/**
 * @SerializedName("jobOffer")
 */
class JobOffer
{
    /**
     * @SerializedName("id")
     */
    private ?int $id = null;

    /**
     * @SerializedName("title")
     */
    private ?string $title = null;

    /**
     * @var string|null $description
     */
    private ?string $description = null;

    /**
     * @var string|null $city
     */
    private ?string $city = null;

    /**
     * @var int|null $salaryMin
     */
    private ?int $salaryMin = null;

    /**
     * @var int|null $salaryMax
     */
    private ?int $salaryMax = null;


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