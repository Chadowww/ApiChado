<?php

namespace App\Services\EntityServices;

use App\Entity\Resume;
use Symfony\Component\HttpFoundation\Request;

class ResumeService
{

    public function createResume(Request $request, string $fileName): Resume
    {
        $uploadedFile = $request->files->get('file');
        $resume = new Resume();
        $resume->setTitle($request->request->get('title'));
        $resume->setFilename($fileName);
        $resume->setCandidate_id((int)$request->request->get('candidate_id'));
        $resume->setCreatedAt(date('Y-m-d H:i:s'));
        $resume->setUpdatedAt(date('Y-m-d H:i:s'));

        return $resume;
    }

    public function updateResume(Resume $resume, Request $request, string $fileName): Resume
    {
        $resume->setTitle($request->request->get('title'));
        $resume->setFilename($fileName);
        $resume->setCandidate_id($request->request->get('candidate_id'));
        $resume->setUpdatedAt(date('Y-m-d H:i:s'));

        return $resume;
    }
}