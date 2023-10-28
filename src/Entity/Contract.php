<?php

namespace App\Entity;

use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @SerializedName("contract")
 */
class Contract
{
    /**
     * @SerializedName("id")
     */
    private ?int $id = null;

    /**
     * @SerializedName("type")
     */
    #[Assert\NotBlank(message: "Type is required")]
    #[Assert\Type("string", message: "Type must be a string")]
    #[Assert\Length(
        min: 3,
        max: 255,
        minMessage: "Type must be at least {{ limit }} characters long",
        maxMessage: "Type must be at least {{ limit }} characters long"
    )]
    private ?string $type = null;

    public function getId(): ?int
    {
        return $this->id;
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