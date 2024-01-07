<?php

namespace App\Services\EntityServices;

use App\Entity\Candidate;
use Symfony\Component\HttpFoundation\Request;

class CandidateService
{
    /**
     * @throws \JsonException
     */
    public function createCandidate(Request $request): Candidate
    {
        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $candidate = new Candidate();
        return $this->setCandidateData($candidate, $data);
    }

    /**
     * @throws \JsonException
     */
    public function updateCandidate(Candidate $candidate, Request $request): Candidate
    {
        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        return $this->setCandidateData($candidate, $data);
    }

    private function setCandidateData(Candidate $candidate, array $data): Candidate
    {
        $candidate->setFirstName($data['firstname']);
        $candidate->setLastName($data['lastname']);
        $candidate->setUser_Id($data['user_id']);
        $candidate->setSlug($data['firstname'], $data['lastname']);
        $candidate->setPhone($data['phone'] ?? null);
        $candidate->setAddress($data['address'] ?? null);
        $candidate->setCity($data['city'] ?? null);
        $candidate->setCountry($data['country'] ?? null);

        return $candidate;
    }
}