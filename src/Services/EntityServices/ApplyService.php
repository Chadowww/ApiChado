<?php

namespace App\Services\EntityServices;

use App\Entity\Apply;
use DateTime;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\Request;

/**
 *  Service for creating and updating Apply entity
 */
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

    /**
     * @throws \JsonException
     */
    public function updateApply(Request $request, Apply $apply): Apply
    {
        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $this->setApplyData($apply, $data);

        return $apply;
    }

    /**
     * @param Apply $apply
     * @param mixed $data
     * @return void
     */
    private function setApplyData(Apply $apply, mixed $data): void
    {
        $apply->setStatus($data['status']);
        $apply->setMessage($data['message'] ?? null);
        $apply->setCandidateId($data['candidateId']);
        $apply->setResumeId($data['resumeId']);
        $apply->setJobofferId($data['jobofferId']);
        if ($apply->getCreatedAt() === null) {
            $apply->setCreatedAt(new DateTimeImmutable());
        }
        $apply->setUpdatedAt(new DateTimeImmutable());
    }

}