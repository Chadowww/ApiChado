<?php

namespace App\Tests;

use App\Entity\Resume;
use App\Services\EntityServices\ResumeService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class ResumeServiceTest extends TestCase
{
    /**
     * @return void
     */
    public function testUpdateResume(): void
    {
        $resume = new Resume();
        $resume->setTitle('Resume Title');
        $resume->setFilename('resume.pdf');
        $resume->setCandidateId(1);

        $data = [
            'title' => 'New Resume Title',
            'filename' => 'new_resume.pdf',
            'candidateId' => 2
        ];

        $request = new Request([], $data, [], [], [], [], []);

        $resumeService = new ResumeService();
        $resumeService->updateResume($resume, $request, 'new_resume.pdf');

        $this->assertEquals($resume->getTitle(), $data['title']);
        $this->assertEquals($resume->getFilename(), $data['filename']);
        $this->assertEquals($resume->getCandidateId(), $data['candidateId']);
    }

    /**
     * @return void
     */
    public function testBuildResume(): void
    {
        $data = [
            'title' => 'Resume Title',
            'filename' => 'resume.pdf',
            'candidateId' => 1
        ];

        $request = new Request([], $data, [], [], [], [], []);

        $resumeService = new ResumeService();
        $resume = $resumeService->buildResume($request, 'resume.pdf');

        $this->assertEquals($resume->getTitle(), $data['title']);
        $this->assertEquals($resume->getFilename(), $data['filename']);
        $this->assertEquals($resume->getCandidateId(), $data['candidateId']);
    }
}
