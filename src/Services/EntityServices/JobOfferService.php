<?php

namespace App\Services\EntityServices;

use App\Entity\JobOffer;
use Symfony\Component\HttpFoundation\Request;

class JobOfferService {

    /**
     * @param Request $request
     * @return JobOffer
     * @throws \JsonException
     */
    public function buildJobOffer(Request $request): JobOffer
    {
        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $jobOffer = new JobOffer();

        $this->setData($jobOffer, $data);
        return $jobOffer;
    }


    /**
     * @param JobOffer $jobOffer
     * @param Request $request
     * @return JobOffer
     * @throws \JsonException
     */
    public function updateJobOffer(JobOffer $jobOffer, Request $request): JobOffer
    {
        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $this->setData($jobOffer, $data);

        return $jobOffer;
    }

    /**
     * @param JobOffer $jobOffer
     * @param mixed $data
     * @return void
     */
    private function setData(JobOffer $jobOffer, mixed $data): void
    {
        $jobOffer->setTitle($data['title']);
        $jobOffer->setDescription($data['description']);
        $jobOffer->setCity($data['city']);
        $jobOffer->setSalaryMin($data['salaryMin']);
        $jobOffer->setSalaryMax($data['salaryMax']);
    }
}