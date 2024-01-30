<?php

namespace App\Entity;

use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

class Contract
{
    /**
     * @SerializedName("id")
     */
    private ?int $contractId = null;

    /**
     * @SerializedName("type")
     */
    #[Assert\NotBlank(message: "Type is required")]
    #[Assert\Type("string", message: "Type must be a string")]
    #[Assert\Regex(
        pattern: '/^[a-zA-Z]+$/',
        message: "Type must contain only letters"
    )]
    #[Assert\Length(
        min: 3,
        max: 50,
        minMessage: "Type must be at least {{ limit }} characters long",
        maxMessage: "Type must be at least {{ limit }} characters long"
    )]
    private ?string $type = null;

    public function getContractId(): ?int
    {
        return $this->contractId;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): void
    {
        $this->type = $type;
    }
}