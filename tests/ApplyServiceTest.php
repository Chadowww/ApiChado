<?php

namespace App\Tests;

use App\Entity\Apply;
use App\Services\EntityServices\ApplyService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class ApplyServiceTest extends TestCase
{
    /**
     * @throws \JsonException
     */
    public function testUpdateApply(): void
   {
       $apply = new Apply();
       $apply->setCandidateId(1);
       $apply->setJobofferId(1);
       $apply->setResumeId(1);
       $apply->setMessage('un message de test');
       $apply->setStatus('pending');

       $data = [
           'candidateId' => 2,
           'jobofferId' => 2,
           'resumeId' => 2,
           'message' => 'Nouveau message de test',
           'status' => 'pending'
       ];

       $request = new Request([], [], [], [], [], [], json_encode($data, JSON_THROW_ON_ERROR));

       $applyService = new ApplyService();

       $applyService->updateApply($apply, $request);

       $this->assertEquals($apply->getCandidateId(), $data['candidateId']);
       $this->assertEquals($apply->getJobofferId(), $data['jobofferId']);
       $this->assertEquals($apply->getResumeId(), $data['resumeId']);
       $this->assertEquals($apply->getMessage(), $data['message']);
       $this->assertEquals($apply->getStatus(), $data['status']);
   }

    /**
     * @throws \JsonException
     */
    public function testCreateApply(): void
   {
       $data = [
           'candidateId' => 1,
           'jobofferId' => 1,
           'resumeId' => 1,
           'message' => 'un message de test',
           'status' => 'pending'
       ];

       $request = new Request([], [], [], [], [], [], json_encode($data, JSON_THROW_ON_ERROR));

       $applyService = new ApplyService();

       $apply = $applyService->createApply($request);

       $this->assertEquals($apply->getCandidateId(), $data['candidateId']);
       $this->assertEquals($apply->getJobofferId(), $data['jobofferId']);
       $this->assertEquals($apply->getResumeId(), $data['resumeId']);
       $this->assertEquals($apply->getMessage(), $data['message']);
       $this->assertEquals($apply->getStatus(), $data['status']);
   }
}
