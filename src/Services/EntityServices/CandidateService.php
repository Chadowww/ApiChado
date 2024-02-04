<?php

namespace App\Services\EntityServices;

use App\Entity\Candidate;

class CandidateService
{
    public function buildCandidate(Candidate $candidate, array $data): Candidate
    {
        foreach ($data as $key => $value) {
            $method = 'set' . ucwords($key);
            if (method_exists($candidate, $method)) {
                $candidate->$method($value);
            }
        }
        return $candidate;
    }
}