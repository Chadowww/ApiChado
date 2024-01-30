<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class Resume
{
    protected ?int $resumeId = null;

    #[Assert\NotBlank(message: "Title is required")]
    #[Assert\Type(type: 'string', message: "Title must be a string")]
    #[Assert\Length(
        min: 3,
        max: 50,
        minMessage: "Title must be at least {{ limit }} characters long",
        maxMessage: "Title must be at least {{ limit }} characters long"
    )]
    protected string $title;

    #[Assert\NotBlank(message: "Filename is required")]
    #[Assert\Type(type: 'string', message: "Filename must be a string")]
    #[Assert\Length(
        min: 3,
        max: 50,
        minMessage: "Filename must be at least {{ limit }} characters long",
        maxMessage: "Filename must be at least {{ limit }} characters long"
    )]
    protected string $filename;
    protected string $createdAt;
    protected string $updatedAt;
    #[Assert\NotBlank(message: "Candidate Id is required")]
    #[Assert\Type(type: 'integer', message: "Candidate Id must be an integer")]
    #[Assert\Positive(message: "Candidate Id must be a positive integer")]
    protected int $candidateId;

    public function getResumeId(): ?int
    {
        return $this->resumeId;
    }

    public function setResumeId(?int $id): void
    {
        $this->resumeId = $id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): void
    {
        $this->filename = $filename;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function setCreatedAt(string $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): string
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(string $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function getCandidateId(): int
    {
        return $this->candidateId;
    }

    public function setCandidateId(int $candidateId): void
    {
        $this->candidateId = $candidateId;
    }
}