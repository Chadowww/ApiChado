<?php

namespace App\Entity;

use DateTime;
use Symfony\Component\Validator\Constraints as Assert;

class Apply
{
    CONST STATUS_DENIED = 'denied';
    CONST STATUS_PENDING = 'pending';
    CONST STATUS_ACCEPTED = 'accepted';
    protected ?int $apply_id;
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
    protected ?int $candidate_id;
    #[Assert\NotBlank(message: "Resume id is required")]
    #[Assert\Type(type: 'integer', message: "Resume id must be an integer")]
    #[Assert\Positive(message: "Resume id must be a positive integer")]
    protected ?int $resume_id;
    #[Assert\NotBlank(message: "Job offer id is required")]
    #[Assert\Type(type: 'integer', message: "Job offer id must be an integer")]
    #[Assert\Positive(message: "Job offer id must be a positive integer")]
    protected ?int $joboffer_id;
    protected DateTime $created_at;
    protected DateTime $updated_at;

    public function getApplyId(): ?int
    {
        return $this->apply_id;
    }

    public function setApplyId(?int $apply_id): void
    {
        $this->apply_id = $apply_id;
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
        return $this->candidate_id;
    }

    public function setCandidateId(?int $candidate_id): void
    {
        $this->candidate_id = $candidate_id;
    }

    public function getResumeId(): ?int
    {
        return $this->resume_id;
    }

    public function setResumeId(?int $resume_id): void
    {
        $this->resume_id = $resume_id;
    }

    public function getJobofferId(): ?int
    {
        return $this->joboffer_id;
    }

    public function setJobofferId(?int $joboffer_id): void
    {
        $this->joboffer_id = $joboffer_id;
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