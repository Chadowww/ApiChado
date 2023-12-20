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
        $candidate->setFirstName($data['firstname']);
        $candidate->setLastName($data['lastname']);
        $candidate->setUserId($data['user_id']);
        $candidate->setPhone($data['phone'] ?? null);
        $candidate->setAddress($data['address'] ?? null);
        $candidate->setCity($data['city'] ?? null);
        $candidate->setCountry($data['country'] ?? null);

        return $candidate;
    }
}