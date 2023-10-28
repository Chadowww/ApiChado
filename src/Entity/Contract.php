<?php

namespace App\Entity;

use Symfony\Component\Serializer\Annotation\SerializedName;

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