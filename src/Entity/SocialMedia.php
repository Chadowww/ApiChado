<?php

namespace App\Entity;

class SocialMedia
{
    protected int $socialeMedia_id;
    protected ?string $linkedin = null;

    protected ?string $github = null;

    protected ?string $twitter = null;

    protected ?string $facebook = null;

    protected ?string $instagram = null;

    protected ?string $website = null;

    public function getLinkedin(): ?string
    {
        return $this->linkedin;
    }

    public function setLinkedin(?string $linkedin): void
    {
        $this->linkedin = $linkedin;
    }

    public function getGithub(): ?string
    {
        return $this->github;
    }

    public function setGithub(?string $github): void
    {
        $this->github = $github;
    }

    public function getTwitter(): ?string
    {
        return $this->twitter;
    }

    public function setTwitter(?string $twitter): void
    {
        $this->twitter = $twitter;
    }

    public function getFacebook(): ?string
    {
        return $this->facebook;
    }

    public function setFacebook(?string $facebook): void
    {
        $this->facebook = $facebook;
    }

    public function getInstagram(): ?string
    {
        return $this->instagram;
    }

    public function setInstagram(?string $instagram): void
    {
        $this->instagram = $instagram;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(?string $website): void
    {
        $this->website = $website;
    }


    public function getAllMedia(): array
    {
        return [
            'linkedin' => $this->getLinkedin(),
            'github' => $this->getGithub(),
            'twitter' => $this->getTwitter(),
            'facebook' => $this->getFacebook(),
            'instagram' => $this->getInstagram(),
            'website' => $this->getWebsite(),
        ];
    }
}