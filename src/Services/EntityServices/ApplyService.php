<?php

namespace App\Services\EntityServices;

use App\Entity\Apply;
use Symfony\Component\HttpFoundation\Request;

class ApplyService
{

    /**
     * @throws \JsonException
     */
    public function createApply(Request $request): Apply
    {
        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $apply = new Apply();

        $this->setApplyData($apply, $data);

        return $apply;
    }

    private function setApplyData(Apply $apply, mixed $data): Apply
    {
        $apply->setStatus($data['status']);
        $apply->setMessage($data['message'] ?? null);
        $apply->setCandidateId($data['candidate_id']);
        $apply->setResumeId($data['resume_id']);
        $apply->setJobofferId($data['joboffer_id']);

        return $apply;
    }
}