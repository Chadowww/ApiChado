<?php

namespace App\Services\EntityServices;

use App\Entity\Resume;
use Symfony\Component\HttpFoundation\Request;

class ResumeService
{

    public function buildResume(Request $request, string $fileName): Resume
    {
        $uploadedFile = $request->files->get('file');
        $resume = new Resume();
        $resume->setTitle($request->request->get('title'));
        $resume->setFilename($fileName);
        $resume->setCandidateId((int)$request->request->get('candidateId'));
        $resume->setCreatedAt(date('Y-m-d H:i:s'));
        $resume->setUpdatedAt(date('Y-m-d H:i:s'));

        return $resume;
    }

    public function updateResume(Resume $resume, Request $request, string $fileName): Resume
    {
        $resume->setTitle($request->request->get('title'));
        $resume->setFilename($fileName);
        $resume->setCandidateId($request->request->get('candidateId'));
        $resume->setUpdatedAt(date('Y-m-d H:i:s'));

        return $resume;
    }
}