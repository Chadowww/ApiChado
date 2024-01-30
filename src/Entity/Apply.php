<?php

namespace App\Entity;

use DateTime;
use DateTimeImmutable;
use Symfony\Component\Validator\Constraints as Assert;

class Apply
{
    CONST STATUS_DENIED = 'denied';
    CONST STATUS_PENDING = 'pending';
    CONST STATUS_ACCEPTED = 'accepted';

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
    }
    protected ?int $applyId;
    #[Assert\NotBlank(message: "Status is required")]
    #[Assert\Type(type: 'string', message: "Status must be a string")]
    #[Assert\Choice(
        choices: [self::STATUS_ACCEPTED, self::STATUS_DENIED, self::STATUS_PENDING],
        message: "Status must be one of the following: 'accepted', 'denied', 'pending'"
    )]
    protected ?string $status;
    #[Assert\Type(type: 'string', message: "Message must be a string")]
    #[Assert\Length(
        min: 0,
        max: 255,
        minMessage: "Message must be at least {{ limit }} characters long",
        maxMessage: "Message must be at least {{ limit }} characters long"
    )]
    protected ?string  $message;
    #[Assert\NotBlank(message: "Candidate id is required")]
    #[Assert\Type(type: 'integer', message: "Candidate id must be an integer")]
    #[Assert\Positive(message: "Candidate id must be a positive integer")
    ]
    protected ?int $candidateId;
    #[Assert\NotBlank(message: "Resume id is required")]
    #[Assert\Type(type: 'integer', message: "Resume id must be an integer")]
    #[Assert\Positive(message: "Resume id must be a positive integer")]
    protected ?int $resumeId;
    #[Assert\NotBlank(message: "Job offer id is required")]
    #[Assert\Type(type: 'integer', message: "Job offer id must be an integer")]
    #[Assert\Positive(message: "Job offer id must be a positive integer")]
    protected ?int $jobofferId;
    protected DateTimeImmutable | string $createdAt;
    protected DateTimeImmutable | string $updatedAt;

    public function getApplyId(): ?int
    {
        return $this->applyId;
    }

    public function setApplyId(?int $applyId): void
    {
        $this->applyId = $applyId;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): void
    {
        $validStatus = [self::STATUS_ACCEPTED, self::STATUS_DENIED, self::STATUS_PENDING];
        if (!in_array($status, $validStatus, true)) {
            throw new \InvalidArgumentException("Invalid status");
        }
        $this->status = $status;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): void
    {
        $this->message = $message;
    }

    public function getCandidateId(): ?int
    {
        return $this->candidateId;
    }

    public function setCandidateId(?int $candidateId): void
    {
        $this->candidateId = $candidateId;
    }

    public function getResumeId(): ?int
    {
        return $this->resumeId;
    }

    public function setResumeId(?int $resumeId): void
    {
        $this->resumeId = $resumeId;
    }

    public function getJobofferId(): ?int
    {
        return $this->jobofferId;
    }
    public function setJobofferId(?int $jobofferId): void
    {
        $this->jobofferId = $jobofferId;
    }

    public function getCreatedAt(): DateTimeImmutable | string
    {
        return $this->createdAt->format('Y-m-d H:i:s');
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable | string
    {
        return $this->updatedAt->format('Y-m-d H:i:s');
    }

    public function setUpdatedAt(DateTimeImmutable $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

}